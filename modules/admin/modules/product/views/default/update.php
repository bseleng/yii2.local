<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>

<!--модальное окно из бутстрап-->
<div id="brandModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<!--Форма редактирования/создания продукта-->
<?php $form = ActiveForm::begin([
    'id' => 'product-form',
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="form-row clearfix">
    <div class="form-group col-md-6">
        <!--  Поле ввода наименование продукта           -->
        <?= $form->field($modelProductForm, 'product_name')
            ->textInput(['maxlength' => 255])
            ->hint('Наименование продукта, которое видит конечный пользователь')
            ->label('Наименование продукта');
        ?>

        <?php
        //Pjax для обновления выпадающего списка после добавления бренда
        Pjax::begin(['id' => 'brand-pjax']);
        ?>
        <div class="input-group">
            <!--  Выпадающий список бренд продукта           -->
            <?= $form->field($modelProductForm, 'brand_id')
                ->dropDownList(
                    ArrayHelper::map(\app\models\Brand::find()
                        ->all(),
                        'brand_id',
                        'brand_name'))
                ->hint('Бренд продукта, который видит конечный пользователь')
                ->label('Бренд продукта')
            ?>

            <!--Кнопка добавления бренда (вызов модального окна)-->
            <span class="input-group-btn">
                    <?= Html::a('Добавить бренд',
                        ['/admin/product/default/create-brand',], [
                            'data-toggle' => 'modal',
                            'data-target' => '#brandModal',
                            'class' => 'btn btn-info',
                            'role' => "button",
                            'style' => '
                                margin-bottom:1rem;
                                font-size: 1.4rem;
                                border-radius: 0 0.3rem 0.3rem 0;
                                background-color: #b6b6b6;
                                border-color: #b6b6b6;
                            '
                        ])
                    ?>
            </span>
        </div>
        <?php Pjax::end(); ?>

        <!--  Поле ввода основная цена продукта           -->
        <div class="form-group col-md-6">
            <?= $form->field($modelProductForm, 'price_base')
                ->textInput(['maxlength' => 6])
                ->hint('Основная цена продукта до применения скидок')
                ->label('Основная цена');
            ?>
        </div>
        <!--  Поле ввода цена продукта  СО СКИДКОЙ         -->
        <div class="form-group col-md-6">
            <?= $form->field($modelProductForm, 'price_discounted')
                ->textInput(['maxlength' => 6])
                ->hint('Цена после применения скидки')
                ->label('Цена со скидкой (если есть)');
            ?>
        </div>
        <!--  Текстовая область описание продукта (для обратной стороны карточки (КРАТКОЕ)          -->
        <?= $form->field($modelProductForm, 'product_description')
            ->textArea(['rows' => '6'])
            ->hint('Описание продукта для обратной стороны карточки в магазине')
            ->label('Краткое описание продукта');
        ?>
    </div>

    <div class="form-group col-md-6">
        <!-- Изображение продукта (для лицевой стороны карточки)       -->
        <p>
            Изображение продукта <?= $modelProductForm->product_name ?>
        </p>
        <img
                src='<?= $modelProductForm->findImagePath() ?>'
                alt="Изображение продукта <?= $modelProductForm->product_name ?>"
                style="max-height:45rem;"
                title="Изображение продукта <?= $modelProductForm->product_name ?>"
                class=""
        >
        <!--  Форма загрузки изображения          -->
        <?= $form->field($modelProductForm, 'imageFile')->fileInput(); ?>
    </div>
</div>

<!--Кнопка сохранения изменений в модель    -->
<?= Html::submitButton('Сохранить', [
    'class' => 'btn btn-primary',
    'style' => 'float:right; margin:1rem;',
    'name' => 'SaveBtn',
    'value' => 'SaveBtn',
]) ?>

<!--Кнопка сохранения изменений в модель с выходом на предыдущую страницу    -->
<?= Html::submitButton('Сохранить и закрыть', [
    'class' => 'btn btn-primary',
    'style' => 'float:right;  margin:1rem;',
    'name' => 'SaveExitBtn',
    'value' => 'SaveExitBtn',
]) ?>
<?php ActiveForm::end(); ?>

<!--Кнопка возврата на предыдушую страницу-->
<?= Html::a(
    'Назад',
    Yii::$app->request->getReferrer(),
    [
        'class' => 'btn btn-warning',
        'style' => 'float: right; margin: 1rem 1rem;'
    ]) ?>


