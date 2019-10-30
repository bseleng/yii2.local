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
}
