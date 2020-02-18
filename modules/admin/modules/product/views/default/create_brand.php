<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\widgets\Pjax;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'brand-form',
    'options' => ['class' => 'form-group', 'data-pjax' => true],
    'action' => 'create-brand',
]);
?>

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
<!--    AJAX-->
    <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-primary',
            'style'=> 'float:right; margin:3rem 1rem 0 0;',
            'name' => 'SaveBtn',

    ]) ?>

</div>

<?php ActiveForm::end(); ?>

<?php
$js = <<<JS
        var brandForm = $('#brand-form');
        brandForm.on('beforeSubmit', function() {
            var data = brandForm.serialize();
            $.ajax({
                // url: brandForm.attr('/admin/product/default/create-brand'),
                url: '/admin/product/default/create-brand',
                type: 'POST',
                data: data,
                success: function (data) {
                    // Implement successful
                    $.pjax.reload({container: '#brand-pjax'}); //почему перезагружает ИД другой страницы?
                    $("#brandModal").modal('hide');
                    document.getElementById('brand-brand_name').value = '';
                    
                //почему не работает reset()
                    // document.getElementById('brand-brand_name').reset();
                    
                // почему не работает jquery?
                    // $("#brand-brand_name").value = '';
                },
                error: function(jqXHR, errMsg) {
                    alert(errMsg);
                }
            });
            return false; // prevent default submit
        });
JS;

$this->registerJs($js);
?>
