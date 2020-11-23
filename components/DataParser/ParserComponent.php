<?php

namespace app\components\DataParser;

use Yii;
use app\models\ImportHistory;
use app\models\Store;
use app\models\StoreProduct;

class ParserComponent
{
    protected $headerValues = [];
    protected $bodyValues = [];
    protected $contentData = [];
    protected $upcKey = "upc";
    protected $titleKey = "title";
    protected $priceKey = "price";

    /**
     * ParserComponent constructor.
     * @param ImportHistory $importHistory
     * @param Store $store
     * @param StoreProduct $storeProduct
     */
    public function __construct(ImportHistory $importHistory, Store $store, StoreProduct $storeProduct)
    {
        $this->importHistory = $importHistory;
        $this->store = $store;
        $this->storeProduct = $storeProduct;
    }

    /**
     * Set Header Values
     */
    private function setHeaderValues()
    {
        $this->headerValues = $this->contentData[0];
    }

    /**
     * Set Body Values
     */
    private function setBodyValues()
    {
        $data = $this->contentData;
        array_shift($data);
        $this->bodyValues = $data;
    }

    /**
     * Generate Data by specific keys
     * @return array
     */
    private function generateData()
    {
        $upcPosition = array_search($this->upcKey, $this->headerValues);
        $titlePosition = array_search($this->titleKey, $this->headerValues);
        $pricePosition = array_search($this->priceKey, $this->headerValues);

        $data = [];
        if (false !== $upcPosition) {
            foreach ($this->bodyValues as $item) {
                if (empty($item[$upcPosition]))
                    continue;
                $data[$this->upcKey][] = $item[$upcPosition];
            }
        }

        if (false !== $titlePosition) {
            foreach ($this->bodyValues as $item) {
                if (empty($item[$upcPosition]))
                    continue;
                $data[$this->titleKey][] = $item[$titlePosition];
            }
        }

        if (false !== $pricePosition) {
            foreach ($this->bodyValues as $item) {
                if (empty($item[$upcPosition]))
                    continue;
                $data[$this->priceKey][] = $item[$pricePosition];
            }
        }

        return $data;
    }

    /**
     * Get rows count
     * @return int
     */
    private function getCount()
    {
        return count($this->bodyValues);
    }

    /**
     * Initialize ParserComponent Class
     * @param $data
     * @return array
     */
    private function generateNewArray($data)
    {
        $this->contentData = $data;
        $this->setHeaderValues();
        $this->setBodyValues();
        $data = $this->generateData();
        $data['count'] = $this->getCount();
        $data['wrong'] = array_key_exists('upc', $data) ? $data['count'] - count($data['upc']) : $data['count'];
        return $data;
    }

    /**
     * Initialize  
     * @param $storeName
     * @param $data
     * @return array
     * @throws \yii\db\Exception
     */
    public function init($storeName, $data)
    {
        $storeId = $this->store->getOrCreateStore($storeName);
        if (empty($data)) {
            $dataNew['count'] = 0;
            $dataNew['wrong'] = 0;
        } else {
            $dataNew = $this->generateNewArray($data);
        }

        $this->saveImportHistory($storeId, $dataNew);

        if($dataNew['wrong'] == 0 && $dataNew['count'] != 0) {
            $this->saveStoreProductBulk($storeId, $dataNew);
        }

        return [
            'data' => [
                'success' => true
            ],
            'code' => 0,
        ];
    }

    /**
     * Save Import History data
     * @param $storeId
     * @param $data
     */
    private function saveImportHistory($storeId, $data)
    {
        $this->importHistory->store_id = $storeId;
        $this->importHistory->total = $data['count'];
        $this->importHistory->status = $data['wrong'] == $data['count'] ? 'wrong' :'success';
        $this->importHistory->succeed = $data['count'] - $data['wrong'];
        $this->importHistory->created_at = date('Y-m-d H:i:s');
        $this->importHistory->save(false);
    }

    /**
     * Save bulk data on StoreProduct
     * @param $storeId
     * @param $data
     * @throws \yii\db\Exception
     */
    private function saveStoreProductBulk($storeId, $data)
    {
        $newData = [];
        for ($i = 0; $i < count($data[$this->upcKey]); $i++) {
            $newData[$i][] = $storeId;
            $newData[$i][] = $data[$this->upcKey][$i];
            $newData[$i][] = $data[$this->titleKey][$i];
            $newData[$i][] = $data[$this->priceKey][$i];
        }
        $command = \Yii::$app->db->createCommand()->batchInsert(
            StoreProduct::tableName(),
            ['store_id', 'upc', 'title', 'price'],
            $newData
        );
        $sql = $command->getRawSql();
        $sql .= ' ON DUPLICATE KEY UPDATE 
        title = IF (upc = VALUES(upc), VALUES(title), title),
        price = IF (upc = VALUES(upc), VALUES(price), price)';
        $command->setRawSql($sql);
        $command->execute();

//        Yii::$app->db
//            ->createCommand()
//            ->batchInsert('store_product', ['store_id', 'upc', 'title', 'price'], $newData)
//            ->execute();
    }
}