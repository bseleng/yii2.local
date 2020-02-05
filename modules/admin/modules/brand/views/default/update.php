<?php
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
            <!--  Поле ввода наименование бренда           -->
            <?= $form->field($modelBrand, 'brand_name')
                ->textInput(['maxlength' => 255])
                ->hint('Наименование бренда, которое видит конечный пользователь')
                ->label('Наименование бренда');
            ?>
    </div>
    <!--Кнопка сохранения изменений в модель    -->
    <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-primary',
            'style'=> 'float:right; margin:1rem;',
            'name' => 'SaveBtn'
    ]) ?>
</form>
<?php ActiveForm::end(); ?>
