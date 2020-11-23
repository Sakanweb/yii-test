<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "store".
 *
 * @property int $id
 * @property string $title
 *
 * @property StoreProduct[] $storeProducts
 */
class Store extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'store';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }

    /**
     * Gets query for [[StoreProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStoreProducts()
    {
        return $this->hasMany(StoreProduct::className(), ['store_id' => 'id']);
    }

    /**
     * Get or create store by title
     * @param $title
     * @return int
     */
    public function getOrCreateStore($title) {
        $id = $this::find()->select('id')->where(['title' => $title])->one()->id;
        if (!is_null($id))
            return $id;

        $this->title = $title;
        $this->save();
        return $this->id;
    }
}
