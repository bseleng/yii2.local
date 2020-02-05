<h1> Product module start page</h1>
<div> you are logged in as <b><?=Yii::$app->userAdmin->identity->username;?></b></div>
<br>

<?php
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Button;
?>

<div class="row">
    <?= Html::a('Создать карточку продукта', ['default/update'],
        ['class'=>'
            btn btn-primary ',
            'style' => '
            margin:1rem;
            float:right;'

        ]); ?>
</div>

<?php

$modelProduct = new \app\models\Product;
//настройка запроса для датаПровайдера
$query = $modelProduct->find();
# НЕПОНЯТНО! почему мне достаточно ВЫБРАТЬ ВСЁ и никак не обозначать джоин на модель Бренд
$query->select(['*']);

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

//pjax  начало
\yii\widgets\Pjax::begin(['id' => 'pjax-container']);
//вывод виджета гридВью с настройками
echo  GridView::widget([
    'dataProvider' => $provider,
    //колонки
    'columns' => [
        //ид продукта из БД
        'product_id',
        //название продукта из БД
        'product_name',
        [
            //привязка к ИД бренда значения имени бренда из связанной таблицы Бренд в модели Продукт
            'attribute' => 'brand_id',
            'value' =>'brand.brand_name',
            'label' => 'Brand Name'
        ],
        //базовая цена продукта из БД
        'price_base',
        //цена со скидкой продукта из БД
        'price_discounted',
        //описание продукта из БД
        'product_description',
        [
            //колонка изображения продукта
            'label' => 'Product Image',
            # НЕПОНЯТНО! почему такой формат
            'format' => 'raw',
            'value' => function($data)
            {
                //возвращает путь к изображению методом модели Продукт
                return Html::img(Url::toRoute($data->findImagePath()),[
                    'alt'=>'изображение '. $data->product_name,
                    'style' => 'width:5rem;'
                ]);
            },
        ],
        [
            //кнопки действий
            'class' => 'yii\grid\ActionColumn',
            //ширина столбца кнопок действий
            'options' => ['style' => 'width:5rem'],
            // кнопки изменения, удаления
            'template' => '{update} {delete}',
            // УРЛ для редактирования конкретной записи модели
            /*'urlCreator' => function($action, $model, $key, $index)
            {
                if ($action === 'update') {
                    return  ['update','id'=>$model->product_id,];
                } if ($action === 'delete') {
                return ['delete', 'id' => $model->product_id,];
            }
//                return ['update','id'=>$model->product_id,];
            },*/
            'buttons' => [
                'delete' => function ($url) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                        'title' => 'Удалить',
                        'aria-label' => "Удалить",
                        'onclick' => "
                            if (confirm('Вы действительно хотите удалить этот продукт?')) {
                                $.ajax('$url', {
                                    type: 'POST'
                                }).done(function(data) {
                                    $.pjax.reload({container: '#pjax-container'});
                                });
                            }
                            return false;
                        ",
                    ]);
                },
            ],

        ],
    ],
]);

//pjax  конец
\yii\widgets\Pjax::end();
?>