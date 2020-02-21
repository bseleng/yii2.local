<h1> Brand module start page</h1>
<div> you are logged in as <b><?=Yii::$app->userAdmin->identity->username;?></b></div>
<br>

<?php
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use app\modules\admin\modules\brand\models\BrandForm;
?>

<div class="row">
    <?= Html::a('Создать бренд', ['default/create'],
        ['class'=>'
            btn btn-primary ',
            'style' => '
            margin:1rem;
            float:right;'

        ]); ?>
</div>

<?php

$modelBrandForm = new BrandForm;
//настройка запроса для датаПровайдера
$query = $modelBrandForm->find();
$query->select(['*']);

//настройки датаПровайдера для гридВью
$provider = new ActiveDataProvider([
    'query' => $query,
    'sort' => [
        'defaultOrder' => [
            'brand_name' => SORT_ASC,
        ],
    ],
    'pagination' => [
        'pageSize' => 20,
    ],
]);

//pjax  начало
\yii\widgets\Pjax::begin(['id' => 'pjax-container']);
//вывод виджета гридВью с настройками
echo  GridView::widget([
    'dataProvider' => $provider,
    //колонки
    'columns' => [
        //ид бренда из БД
        [
            'attribute' => 'brand_id',
            'format' => 'text',
            'label' => 'ID Бренда',
            'options' => ['style' => 'width:5%;'],
        ],
        //название бренда из БД
        [
            'attribute' => 'brand_name',
            'format' => 'text',
            'label' => 'Название Бренда',
            'options' => ['style' => 'width:80%;'],
        ],
        //количество товаров этого бренда из БД
        [
            'format' => 'text',
            'label' => 'Продуктов',
            'value' => function ($model, $key, $index, $column) {
                return $model->countProducts();
            },
        ],
        [
            //кнопки действий
            'class' => 'yii\grid\ActionColumn',
            //ширина столбца кнопок действий
            'options' => ['style' => 'width:5%'],
            // кнопки изменения, удаления
            'template' => '{update} {delete}',
            'buttons' => [
                'delete' => function ($url) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', '#', [
                        'title' => 'Удалить',
                        'aria-label' => "Удалить",
                        'onclick' => "
                            if (confirm('Вы действительно хотите удалить этот бренд?')) {
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