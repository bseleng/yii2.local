

<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\data\ActiveDataProvider;
?>

<h3> Export page</h3>
<div>.</div>
<div>.</div>
<?php echo '<h5>' .'Чистый ГЕТ ' . '</h5>'; var_dump(Yii::$app->request->get()); ?>
<div>.</div>
<div>.</div>
<?php echo '<h5>' .'ГЕТ после декода ' . '</h5>' ; var_dump(json_decode(Yii::$app->request->get('getParams'), true)['ProductSearchForm']); ?>
<div>.</div>
<div>.</div>
<?php echo '<h5>' . 'Модели ' . '</h5>'; var_dump($model->search()->getModels()[9]['brand']['brand_name']); ?>
<div>.</div>
<div>.</div>

<?php echo '<h5>' . 'Всего товаров ' . '</h5>'; var_dump($model->search()->getTotalCount()); ?>

<?php $model->writeToFile() ?>
<?php $model->export() ?>

<?php

//настройка запроса для датаПровайдера

//pjax  начало
\yii\widgets\Pjax::begin(['id' => 'pjax-container']);
//вывод виджета гридВью с настройками
echo  GridView::widget([
    'dataProvider' => $model->search(),
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
            'buttons' => [

### НЕПОНЯТНО в принципе как  это работает

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
