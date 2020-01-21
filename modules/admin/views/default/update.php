<?php
use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<?php $form = ActiveForm::begin([
    'id' => 'profile-form',
]); ?>

<form>
    <div class="form-row clearfix">
        <div class="form-group col-md-6">
            <!--  Поле ввода наименование продукта           -->
            <?= $form->field($modelProduct, 'product_name')
                ->textInput(['maxlength' => 255])
                ->hint('Наименование продукта, которое видит конечный пользователь')
                ->label('Наименование продукта');
            ?>
            <!--  Выпадающий список бренд продукта           -->
            <?= $form->field($modelProduct, 'brand_id')
                ->dropDownList(
                    ArrayHelper::map(\app\models\Brand::find()
                        ->joinWith('product')
                        ->andWhere(['not',['product.product_name'=>null]])
                        ->all(),
                        'brand_id','brand_name'))
                ->hint('Бренд продукта, который видит конечный пользователь')
                ->label('Бренд продукта');
            ?>
            <!--  Поле ввода основная цена продукта           -->
            <div class="form-group col-md-6">
                 <?= $form->field($modelProduct, 'price_base')
                    ->textInput(['maxlength' => 6])
                    ->hint('Основная цена продукта до применения скидок')
                    ->label('Основная цена');
                ?>
            <!--  Поле ввода цена продукта  СО СКИДКОЙ         -->
            </div>
            <div class="form-group col-md-6">
                 <?= $form->field($modelProduct, 'price_discounted')
                    ->textInput(['maxlength' => 6])
                    ->hint('Цена после применения скидки <br>  (ЕСЛИ СКИДКИ НЕТ, СТАВИМ 0!)')
                    ->label('Цена со скидкой (если есть)');
                ?>
            </div>
            <!--  Текстовая область описание продукта (для обратной стороны карточки (КРАТКОЕ)          -->
            <?= $form->field($modelProduct, 'product_description')
                ->textArea(['rows' => '6'])
                ->hint('Описание продукта для обратной стороны карточки в магазине')
                ->label('Краткое описание продукта');
            ?>

        </div>
        <div class="form-group col-md-6">
            <!-- Изображение продукта (для лицевой стороны карточки)       -->
            <p>
                Изображение продукта <?= $modelProduct->product_name ?>
            </p>
            <img
                src='<?= $modelProduct->findImagePath() ?>'
                alt="Изображение продукта <?= $modelProduct->product_name ?>"
                style="max-height:45rem;"
                title="Изображение продукта <?= $modelProduct->product_name ?>"
                class=""
            >
        </div>
    </div>
    <!--Кнопка сохранения изменений в модель    -->
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
</form>

<?php ActiveForm::end(); ?>
