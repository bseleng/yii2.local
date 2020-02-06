<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<?php $form= ActiveForm::begin([
    'id' => 'brand-form',
    'options' => ['class' => 'form-group'],
    'action' => 'create-brand',
]); ?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title">Новый бренд</h4>
</div>
<div class="form-row clearfix">
    <div class="form-group col-md-8" style='margin-top: 1rem;'>
        <!--  Поле ввода наименование бренда           -->
        <?= $form->field($modelBrand, 'brand_name')
            ->textInput(['maxlength' => 50])
            ->hint('Наименование бренда, которое видит конечный пользователь')
            ->label('Наименование бренда');
        ?>
</div>
<!--Кнопка сохранения изменений в модель    -->
<?= Html::submitButton('Сохранить', [
        'class' => 'btn btn-primary',
        'style'=> 'float:right; margin:3rem 5rem 0 0;',
        'name' => 'SaveBtn'
]) ?>
</div>

<?php ActiveForm::end(); ?>
