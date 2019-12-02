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
    public $orderArr = [];

    /**
     * возвращает актуальное количество позиций в заказе
     * @return string возвращает актуальное количество позиций в заказе
     */
    protected function getCartProductCount()
    {
        if(!$this->orderArr) {
            return 0;
        } else {
            return array_sum($this->orderArr);
        }
    }

    /**
     * добавляет переданное количество указанной позиции в заказ
     * и возвращает актуализированный массив заказа
     *
     * проверяет есть ли в массиве заказа  укзанный ИД продукта
     *     если нет - добавляет массив в формате ["ИД"] => количество, согласно количества в пост запросе
     *     если да - увеличивает количество требуемого ИД на количество, согласно количества в пост запросе
     * возвращает актуализированный массив заказа
     *
     * @param $productId int значение ИД продукта из БД отправляемое пост запросом
     * @param $quantity int значение количества соответствующего ИД продукта отправляемое пост запросом
     * @return array актуализированный массив заказа 'order'
     */
    public function addProduct($productId, $quantity)
    {
        if(isset($this->orderArr[$productId])) {
            $quantity = $this->orderArr[$productId] + $quantity;
        }

        $this->orderArr[$productId] = $quantity;

        return $this->orderArr;





    }

    /**
     * возвращает массив в формате ["ИД продукта"] => цена из БД, на основании состава заказа
     *
     * делает запрос в БД, выбирает доп столбец с финальной ценой
     * делает массив из ИД продуктов, на основании состава заказа
     * выбирает записи из БД с ИД продуктов из состава заказа
     * формирует массив актуальный цен согласно состава заказа
     *
     * @return array массив в формате ["ИД продукта"] => цена из БД, на основании состава заказа
     */
    protected function getProductPricesArr()
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


        $idArr = (array_keys($this->orderArr));
        $query->andWhere(['product_id' => $idArr]);
        $arrModelShoppingCart = $query->all();

        foreach ($arrModelShoppingCart as $key => $field) {
            $productPricesArr[$field["product_id"]] = $field["price_final"];
        }

        return $productPricesArr;
    }


    /**
     * возвращает сумму всех цен позиций в заказе
     *
     * если массив заказа пуст - возвращает 0
     * иначе проходит по всему заказу умножая цену каждого ИД продукта на количество этого продукта
     *
     * @return float|int сумма всех цен позиций в заказе
     */
    protected function getCartProductPrices()
    {
        $cartProductPrices = 0;

        if(!$this->orderArr) {
            return $cartProductPrices;
        } else {
            foreach ($this->orderArr as $productId => $quantity) {
                $cartProductPrices += $quantity * $this->getProductPricesArr()[$productId];
            }
        }

        return $cartProductPrices;
    }


    /**
     * возвращает строку закодированую в JSON с общим количеством и ценой продуктов в заказе
     *
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

