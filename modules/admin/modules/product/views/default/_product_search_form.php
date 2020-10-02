<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php

$form = ActiveForm::begin([
    'id' => 'product-search-form',
    'method' => 'get',
    'options' => ['class' => 'form-group'],
    'action' => 'index',
]);


 ?>
    <div>
        <div class="col-lg-2" style='margin-top: 1rem;'>
            <!--  Поле поиска наименование бренда           -->
            <?= $form->field($modelProductSearchForm, 'product_name')
                ->textInput(['maxlength' => 50])
                ->label('Название продукта');
            ?>
        </div>

        <div class="col-lg-2" style='margin-top: 1rem;'>
            <!--  Поле поиска наименование бренда           -->
            <?= $form->field($modelProductSearchForm, 'brandName')
                ->textInput(['maxlength' => 50])
                ->label('Название бренда');
            ?>
        </div>

        <div class="col-lg-2" style='margin-top: 1rem;'>
            <!--  Поле поиска наименование бренда           -->
            <?= $form->field($modelProductSearchForm, 'product_description')
                ->textInput(['maxlength' => 50])
                ->label('Описание продукта');
            ?>
        </div>


        <div class="col-lg-2" style='margin-top: 1rem;'>
            <!--  Поле поиска наименование бренда           -->
            <?= $form->field($modelProductSearchForm, 'minPrice')
                ->textInput(['maxlength' => 50])
                ->label('Минимальная цена');
            ?>
        </div>

        <div class="col-lg-2" style='margin-top: 1rem;'>
            <!--  Поле поиска наименование бренда           -->
            <?= $form->field($modelProductSearchForm, 'maxPrice')
                ->textInput(['maxlength' => 50])
                ->label('Максимальная цена');
            ?>
        </div>

            <?= Html::submitButton('Найти', ['class' => 'btn btn-primary', 'style' => 'margin-top: 2.9rem; padding: 0.6rem 1.5rem;']) ?>

<!--            --><?//= Html::resetButton('Очистить', ['class' => 'btn btn-primary', 'style' => 'margin-top: 2.9rem; padding: 0.7rem 2rem;']) ?>
            <?= Html::a('Очистить', ['index'],
                [
                        'class' => 'btn btn-primary',
                        'style' => 'margin-top: 2.9rem; margin-left: 1rem; padding: 0.6rem 1.5rem;',
                        'id' => 'reset-btn',
                ]) ?>
    </div>

<?php ActiveForm::end(); ?>

