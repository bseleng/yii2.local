<?php

namespace app\models;

use yii\base\Model;
use \app\models\Product;

class ShoppingCart extends Product
{
    public $productCountText = 'В корзине товаров: ';
    public $productPriceText = 'На сумму: ';
    public $orderArr;
    public $productQuantity;
//    $modelShoppingCart->productQuantity = $_SESSION['order'][$modelShoppingCart->product_id]['quantity'];

    //баг с подсчётом после обновления страницы
    /**
     * возвращает актуальное количество позиций в заказе
     * @return string возвращает актуальное количество позиций в заказе
     */
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

    }
}

