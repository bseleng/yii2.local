<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use \app\models\Brand;
use yii\helpers\ArrayHelper;
?>

<?php



    $form = ActiveForm::begin([
        'method' => 'get',
        'action' => Url::to(['/shop']),
        'fieldConfig' => [
                'options' => [
                    'class' => 'bs-form',

                ]
            ]
    ]);
?>

<?php echo $form
    ->field($modelProductSearch, 'brand_id')
    ->dropDownList(
        [0 => 'Все бренды'] +
        ArrayHelper::map(Brand::find()
            ->joinWith('product')
            ->andWhere(['not',['product.product_name'=>null]])
            ->all(),
            'brand_id','brand_name'));
?>



<?= $form
        ->field($modelProductSearch, 'productNameSort')
        ->dropDownList([
            0=>'А - Я',
            1=>'Я - А',
            2=>'Сначала дешевле',
            3=>'Сначала дороже',
            4=>'Сначала со скидками',

        ]);
?>

<?= $form
        ->field($modelProductSearch, 'productMinPrice')
        ->input('number', ['step'=>500, 'placeholder'=>'Мин. цена']);
?>

<?= $form
        ->field($modelProductSearch, 'productMaxPrice')
        ->input('number', ['step'=>500, 'placeholder'=>'Макс. цена']);
?>

<div class="form-group", style="display: block; clear: both">
    <?= Html::submitButton('Найти', [
        'class' => 'btn btn-warning btn-no-corners',
    ]) ?>
</div>

<?php ActiveForm::end(); ?>
