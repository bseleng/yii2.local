<h1> Product module start page</h1>
<div> you are logged in as <b><?=Yii::$app->userAdmin->identity->username;?></b></div>
<br>

<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?= $this->render('_product_search_form', ['modelProductSearchForm' => $modelProductSearchForm]); ?>

<div class="row">
    <?= Html::a('Создать карточку продукта', ['default/create'],
        ['class'=>'
            btn btn-primary ',
            'style' => '
            margin:1rem;
            float:right;'

        ]); ?>
</div>

<?php

//настройка запроса для датаПровайдера

//pjax  начало
\yii\widgets\Pjax::begin(['id' => 'pjax-container']);
//вывод виджета гридВью с настройками
echo  GridView::widget([
    'dataProvider' => $modelProductSearchForm->search(),
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

<div>
<?php
$form = ActiveForm::begin([
'id' => 'export-form-xlsx',
'method' => 'get',
'options' => ['class' => 'form-group'],
'action' => 'export-xlsx',
]);
?>
<?= Html::submitButton('Экспорт в .xlsx',
    [
        'class' => 'btn btn-success',
        'style' => 'padding: 0.6rem 1rem; float:right; margin: 0 0.5rem;'
    ])
?>

<?= Html::hiddenInput ( 'getParams', json_encode(Yii::$app->request->get())) ?>

<?php ActiveForm::end(); ?>
</div>

<div>
<?php
$form = ActiveForm::begin([
    'id' => 'export-form-csv',
    'method' => 'get',
    'options' => ['class' => 'form-group'],
    'action' => 'export-csv',
]);
?>
<?= Html::submitButton('Экспорт в .csv',
    [
        'class' => 'btn btn-default',
        'style' => 'padding: 0.6rem 1rem; float:right; margin: 0 0.5rem;'
    ])
?>

<?= Html::hiddenInput ( 'getParams', json_encode(Yii::$app->request->get())) ?>

<?php ActiveForm::end(); ?>
</div>