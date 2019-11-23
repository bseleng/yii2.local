<?php

namespace app\models;

use yii\base\Model;
use \app\models\Product;

class ShoppingCart extends ProductSearch
{
    //string строка в виджете корзины перед значением общего количества товаров
    public $productCountText = 'В корзине товаров: ';
    //string строка в виджете корзины перед значением суммы цен количества товаров
    public $productPriceText = 'На сумму: ';
    // array массив состава заказа 'order' из сессии
    public $orderArr;
    // array массив со значением ИД продукта 'productId' из сессии
    public $productId;
    // array массив со значением количества продукта 'quantity' из сессии
    public $productQuantity;

//    $modelShoppingCart->productQuantity = $_SESSION['order'][$modelShoppingCart->product_id]['quantity'];


    /**
     * возвращает актуальное количество позиций в заказе
     * @return string возвращает актуальное количество позиций в заказе
     */
    public function getCartProductCount()
    {
        if(!$this->orderArr) {
            return 0;
        } else {
            return array_sum(array_column($this->orderArr, 'quantity'));
        }
    }

    /**
     * проверяет есть ли в массиве заказа позиция с укзанным ИД продукта
     *     если нет - добавляет массив в формате ["ИД"] => количество, согласно количества в пост запросе
     *     если да - увеличивает количество требуемого ИД на количество, согласно количества в пост запросе
     * возвращает обновленный массив заказа
     *
     * @return array обновлённый массив заказа 'order' из сессии
     */
    public function addProduct()
    {
        if(!isset($this->orderArr[$this->productId]['quantity'])){
            $this->orderArr[$this->productId] =
                [
                    'productId' => $this->productId,
                    'quantity' => $this->productQuantity,
                ];
        } else {
            $this->orderArr[$this->productId] =
                [
                    'productId' => $this->productId,
                    'quantity' => $this->orderArr[$this->productId]['quantity'] + $this->productQuantity,
                ];
        }

        return $this->orderArr;


        //старый вариант
        /*        $order[$productId] =
            [
                'productId' => $productId,
                'quantity' => $quantity,
            ];*/
    }

    /**
     * @return array
     */
    public function getProductPricesArr()
    {
        $query = $this->find();
        $query->select([
            '*',
            'IF (
                product.price_discounted = 0, 
                product.price_base , 
                product.price_discounted) 
            AS price_final'
        ]);

        if(!$this->orderArr) {
            $productPricesArr = [];

        } else {
            $idArr = (array_column($this->orderArr, 'productId'));
            $query->andWhere(['product_id' => $idArr]);
            $arrModelShoppingCart = $query->all();

            foreach ($arrModelShoppingCart as $key => $field) {
                $productPricesArr[$field["product_id"]] = $field["price_final"];

            }
        }


        return $productPricesArr;
    }


    /**
     * @return float|int
     */
    public function getCartProductPrices()
    {

        if(empty($this->getProductPricesArr())) {
            $cartProductPrices = 0;
        } else {
            foreach ($this->orderArr as $productId => $targetArr) {
                $cartProductPrices += $targetArr['quantity'] * $this->getProductPricesArr()[$productId];
            }
        }

        return $cartProductPrices;
    }


    /**
     * @return string
     */
    public function getShoppingCartValues()
    {
        return json_encode(
            [
                'productTotalCount' => $this->getCartProductCount(),
                'productTotalSum' => $this->getCartProductPrices(),
            ]
        );

    }



}

