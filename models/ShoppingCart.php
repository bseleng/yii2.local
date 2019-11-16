<?php

namespace app\models;

class ShoppingCart extends Product
{
    public $productCountText = 'В корзине товаров: ';
    public $productPriceText = 'На сумму: ';
    public $orderArr;


    public function getCartProductCount()
    {
        $sumArr = [0];
        if(!$this->orderArr) {
            return $this->productCountText . 0;
        } else {

            return $this->productCountText . array_sum(array_column($this->orderArr, 'quantity'));
        }
    }
}

