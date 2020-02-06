<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php
$request = Yii::$app->request;
if ($request->get('id')) {
    $form = ActiveForm::begin([
        'id' => 'brand-form',
        'options' => ['class' => 'form-group'],
        'action' => 'update?id=' . $modelBrand->brand_id,
    ]);
} else {
    $form = ActiveForm::begin([
        'id' => 'brand-form',
        'options' => ['class' => 'form-group'],
        'action' => 'create',
    ]);

}
 ?>
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