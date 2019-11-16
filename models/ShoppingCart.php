<?php

namespace app\models;

class ShoppingCart extends Product
{
    public $productCountText = 'В корзине товаров: ';
    public $productPriceText = 'На сумму: ';
    public $orderArr;


    public function getCartProductCount()
    {

        if(!$this->orderArr) {
            return $this->productCountText . 0;
        } else {
            return $this->productCountText . array_sum(array_column($this->orderArr, 'quantity'));
        }
    }

    public  function getProductQuantity()
    {
        $productQuantity = $_SESSION['order'][$this->product_id]['quantity'];
        if(!isset($productQuantity)) {
            $productQuantity = 100;
        } else {
            $productQuantity +1;
        }

        return $productQuantity;
    }
}

