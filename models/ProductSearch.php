<?php

namespace app\models;

use \app\models\Product;
use \yii\db\Query;
use Yii;
use app\components\Helper;

class ProductSearch extends Product
{
    public $productNameSort;
    public $productMinPrice;
    public $productMaxPrice;
    // alias для объединения цен в одну
    public $price_final;

    public function rules()
    {
        return [
            //
            [
                [
                    'brand_id',
                    'productNameSort',
                    'productMinPrice',
                    'productMaxPrice',
                ],
                'integer'
            ],
        ];
    }


    /**
     * создаёт часть yii2 ActiveRecordInterface запроса с учётом фильтров (
     *      максимальная цена,
     *      минимльная цена,
     *      бренд,
     *      сортировка по алфавиту
     *      сортировка по ценам
     * ), полученных от пользователя
     *
     * @return \yii\db\ActiveQuery часть yii2 запроса с фильтрами
     */
    public function getFilterQuery()
    {
        $query = $this->find();
        $query->select([
            '*',
            'IF (
                product.price_discounted = 0, 
                product.price_base , 
                product.price_discounted) 
            AS price_final'

/*          `CASE
                WHEN product`.`price_discounted=0
                THEN product`.`price_base
                ELSE product`.`price_discounted
            END` AS `price_final` FROM `product`

        ставит лишние кавычки, не понял синтаксис
*/
        ]);

        $query->from('product');
        $query->joinWith('brand');

        //почему мне нужно приводить к строковому 0, когда в rules у меня  int?
        if ($this->productMaxPrice || $this->productMaxPrice === '0') {
            $query->andWhere(
                ['OR',
                    ['AND',
                        ['=','product.price_discounted',0],
                        ['<=','product.price_base',$this->productMaxPrice],
                    ],
                    ['AND',
                        ['<>','product.price_discounted',0],
                        ['<=','product.price_discounted',$this->productMaxPrice]
                    ],
                ]
            );
        }

        if ($this->productMinPrice || $this->productMinPrice === '0') {
            $query->andWhere(
                ['OR',
                    ['AND',
                        ['=','product.price_discounted',0],
                        ['>=','product.price_base',$this->productMinPrice],
                    ],
                    ['AND',
                        ['<>','product.price_discounted',0],
                        ['>=','product.price_discounted',$this->productMinPrice]
                    ],
                ]
            );
        }

        if ($this->brand_id) {
            $query->andWhere(['product.brand_id'=>$this->brand_id]);
        }

//clone $query
        if ($this->productNameSort == 0) {
            $query->orderBy("product.product_name ASC");
        } elseif ($this->productNameSort == 1) {
            $query->orderBy("product.product_name DESC");
        } elseif ($this->productNameSort == 2) {
            $query->orderBy('price_final ASC');
        } elseif ($this->productNameSort == 3) {
            $query->orderBy('price_final DESC');
        } elseif ($this->productNameSort == 4) {
            $query->orderBy('price_discounted DESC, price_base DESC');
//            $query->orderBy(['(price_discounted - price_base)'=> SORT_DESC]);
        }

        return $query;

    }

    /**
     * находит заданное количество объектов, начиная с указанного индекса
     * и возвращает массив объектов отсортированный в заданном пользователем порядке
     *
     * @param int $offset значение смещения
     * @param int $limit требуемуе количество объектов
     * @return array|\yii\db\ActiveRecord[] массив с заданным количеством объектов,
     *                                      выбранный, начиная с указанного значения смещения
     */
    public function search($offset, $limit=3)
    {
        $query = $this->getFilterQuery();
        $query->limit($limit);
        if (isset($offset)) {
            $query->offset($offset);
        }

        $arrModelProduct = $query->all();

        return $arrModelProduct;
    }

    /**
     * подсчитывает общее количество продуктов
     * @return int|string общее количество продуктов (записей)
     */
    public function countGoods()
    {
        $query = $this->getFilterQuery();
        $goodsCount = $query->count();

        return $goodsCount;
    }

    /**
     * определяет есть ли ещё товары для следующего запроса
     * @param int $limit лимит товаров, который показывается пользователю
     * @param int $page текущий номер страницы
     * @return bool булевое значение, определяющее последняя страница или нет
     *         (т.е. true == товары ещё есть
     *         false == товары закончились)
     */
    public function findLastPage($limit, $page)
    {
        $count = $this->countGoods();
        if (ceil($count/$limit) > $page) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * устанавливает значения ГЕТ параметров по умолчанию на
     * Все бренды (0), по убыванию (0)
     * если строка была ПУСТОЙ!
     *
     * @param string $getParams строка ГЕТ параметров в ДЖЕЙСОН, без открывающей и закрывающей фигурных скобок
     * @return string строка ГЕТ параметров в ДЖЕЙСОН, без открывающей и закрывающей фигурных скобок
     *                со значениями по умолчанию
     */
    public function setDefaultFilter($getParams)
    {
        if (empty($getParams)) {
            $getParams = 'ProductSearch:{brand_id:0,productNameSort:0}';
        }

        return $getParams;
    }

    /**
     * Сообщение, когда товаров по заданным фильтрам не найдено
     * @return string текст сообщения, который будет показан пользователю
     */
    public function noProductFound()
    {
        return 'Товаров не найдено, пожалуйста попробуйте изменить критерии поиска';
    }

}
