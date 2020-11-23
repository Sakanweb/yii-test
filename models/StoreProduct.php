<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store_product".
 *
 * @property int $id
 * @property int $store_id
 * @property int $upc
 * @property string $title
 * @property int $price
 *
 * @property Store $store
 */
class StoreProduct extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store_product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['store_id', 'upc', 'title', 'price'], 'required'],
            [['store_id', 'upc'], 'integer'],
            [['title', 'price'], 'string', 'max' => 255],
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
            'upc' => 'Upc',
            'title' => 'Title',
            'price' => 'Price',
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

    public function getExistingUpc($data) {
        return $this::find()->select('id')->where(['IN', 'upc', $data])->all();
    }
}
