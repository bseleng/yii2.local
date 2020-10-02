<?php

namespace app\modules\admin\modules\product\models;

use app\models\Product;
use yii\data\ActiveDataProvider;

class ProductSearchForm extends Product
{
    public $minPrice;
    public $maxPrice;
    public $brandName;

    public function rules()
    {
        return [
            [['product_name', 'product_description', 'brandName'], 'string'],
            [['brand_id',], 'integer'],
            [['minPrice', 'maxPrice'], 'double'],
            [['price_base', 'price_discounted',], 'number'],
        ];
    }


    /**
     * ищет бренд похожий на пользовательский ввод
     *
     * @return mixed ид бренда либо NULL
     */
    public function getBrandId()
    {
        if ($this->brandName) {
            $brandId = $this::find()
                ->leftJoin('brand', '`brand`.`brand_id` = `product`.`brand_id`')
                ->where(['like', 'brand_name', $this->brandName])
                ->one();

            return $brandId->brand_id;
        }
    }

    /**
     * применяет к объекту запроса фильтр по минимальной цене
     *
     * @param $query объект запроса с любыми фильтрами до него
     * @return mixed объект запроса с применёнными фильтрами минимальной цены
     */
    public function filterMinPrice($query)
    {
        $query->andWhere([
            'OR',
            [
                'AND',
                ['<>', 'price_discounted', 0],
                ['>=', 'price_discounted', $this->minPrice],
            ],
            [
                'AND',
                ['=', 'price_discounted', 0],
                ['>=', 'price_base', $this->minPrice],
            ],
        ]);
        return $query;
    }

    /**
     * применяет к объекту запроса фильтр по максимальной цене
     *
     * @param $query объект запроса с любыми фильтрами до него
     * @return mixed объект запроса с применёнными фильтрами максимальной цены
     */
    public function filterMaxPrice($query)
    {
        $query->andWhere([
            'OR',
            [
                'AND',
                ['<>', 'price_discounted', 0],
                ['<=', 'price_discounted', $this->maxPrice],
            ],
            [
                'AND',
                ['=', 'price_discounted', 0],
                ['<=', 'price_base', $this->maxPrice],
            ],
        ]);
        return $query;
    }

    /**
     * ищет по указанным критериям (бренд, имя, описание, минимальная цена, максимальная цена)
     *
     * @return ActiveDataProvider
     */
    public function search()
    {

        $query = $this->find();
# НЕПОНЯТНО! почему мне достаточно ВЫБРАТЬ ВСЁ и никак не обозначать джоин на модель Бренд

        if ($this->brandName) {
            $query->andWhere('brand_id=:brand_id')
                ->addParams([':brand_id' => $this->getBrandId()]);
        }

        // непонятно как найти в документации тот факт, что защищает от инъекций
        if ($this->product_name) {
            $query->andWhere(['like', 'product_name', $this->product_name]);
        }

        if ($this->product_description) {
            $query->andWhere(['like', 'product_description', $this->product_description]);
        }

        if ($this->minPrice) {
            $this->filterMinPrice($query);
        }

        if ($this->maxPrice) {
            $this->filterMaxPrice($query);
        }

//having SQL
//настройки датаПровайдера для гридВью
        $provider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'product_name' => SORT_ASC,
                ],
            ],
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $provider;
    }


}

