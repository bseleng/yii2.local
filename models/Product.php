<?php

namespace app\models;

use \yii\db\ActiveRecord;
use \yii\BaseYii;


class Product extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return \Yii::$app->db;
    }

    public function getBrand()
    {
        return $this->hasOne(Brand::class, ['brand_id' => 'brand_id']);
    }

    /**
     * выбирает стиль, согласно выбранному лимиту длины названия
     * @param int $limit максимальная длина названия в символах (больший шрифт)
     * @return string название стиля для названия товара в карточке
     */
    public function selectNameStyle($limit=15)
    {
        $length = strlen($this->product_name);
        if ($length <= $limit) {
            return 'bs-product-title';
        } else {
            return 'bs-product-long-title';
        }
    }

    /**
     * выбирает стиль цены (1я строка)
     * если скидки нет - скрывает значение (hidden)
     * @return string стиль цены 1й строки
     */
    public function selectNewPriceStyle()
    {
        if ($this->price_discounted == 0) {
            return 'hidden';
        } else  {
            return 'bs-new-price';
        }
    }

    /**
     * выбирает стиль цены 2й строки
     * если есть скидка - выбирает зачёркнутый стиль для базовой цены
     * @return string стиль цены 2й строки
     */
    public function selectOldPriceStyle()
    {
        if ($this->price_discounted == 0) {
            return 'bs-new-price';
        } else  {
            return 'bs-old-price';
        }
    }

    /**
     * формирует строковое значение пути расположения
     * файла изображения продукта (в нижнем регистре)
     * @return string путь к файлу изображения продукта
     *
     */
    public function findImagePath()
    {
        $imagePath = '/' . 'uploads/shop/pic';
        $imagePath .= '/' . $this->brand->brand_name;
        $imagePath .= '/' . $this->image_path;

        return  strtolower($imagePath);
    }


    public function getProductId()
    {
        return $this->product_id;
    }

    public function setOrder($productId, $quantity)
    {
        $session['order'] = [
            'productId' => $productId,
            'quantity' => $quantity,
        ];

        return $session;
    }




}

