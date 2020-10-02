<?php

namespace app\models;

use \yii\db\ActiveRecord;

class Brand extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%brand}}';
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return \Yii::$app->db;
    }


    public function getProduct()
    {
        return $this->hasMany(Product::class, ['brand_id' => 'brand_id']);
    }

    /**
     * возвращает количество продуктов соответствующего ИД бренда в модели  PRODUCT
     * @return int|string
     */
    public  function countProducts()
    {
        return Product::find()->where(['brand_id' => $this->brand_id])->count();
    }
}
