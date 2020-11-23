<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "import_history".
 *
 * @property int $id
 * @property int $store_id
 * @property int|null $count
 * @property string|null $status
 * @property int|null $total
 *
 * @property Store $store
 */
class ImportHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'import_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id'], 'required'],
            [['store_id', 'total', 'succeed'], 'integer'],
            ['created_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['status'], 'string'],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::className(), 'targetAttribute' => ['store_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'store_id' => 'Store ID',
            'total' => 'Total',
            'succeed' => 'Succeed',
            'status' => 'Status',
            'created_at' => 'Created',
        ];
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'store_id']);
    }

    public function getStoreTitle()
    {
        return $this->store->title;
    }

    public function getWrong()
    {
        return $this->total - $this->succeed;
    }
}
