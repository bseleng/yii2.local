<?php

namespace app\modules\admin\modules\product\models;

use \app\models\Product;

class ProductForm extends Product
{
    public function rules()
    {
        return [
            [['product_name', 'product_description', 'image_path',], 'string'],
            [['price_base', 'price_discounted',], 'number'],
            [['brand_id',], 'integer'],
            //чтоб избежать ошибку, когда нет цены
            [['price_base', 'price_discounted',], 'default', 'value' => 0],

            // необходимые поля
            [['product_name', 'brand_id',], 'required'],
        ];
    }


}

