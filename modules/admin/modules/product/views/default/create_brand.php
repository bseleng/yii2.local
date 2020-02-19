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
        // поле ввода названия бренда
        var brandForm = $('#brand-form');
        // перехватывает обычную отправку формы.
        // передаёт данные от пользователя асинхронно
        brandForm.on('beforeSubmit', function()
        {
            //подготавливает пользовательский ввод для передачи через  AJAX
            var data = brandForm.serialize();
            //передаёт пользовательский ввод на экшн CreateBrand
            $.ajax({
                // async: false,
                url: '/admin/product/default/create-brand',
                type: 'POST',
                data: data,
                success: function(data)
                 {
                    // Implement successful
                    //очищает пользовательский ввод
                    document.getElementById('brand-brand_name').value = '';
                    //перезагружает выпадающий список, обёрнутый в пЯКС
                    $.pjax.reload({container: '#brand-pjax'}); //почему перезагружает ИД другой страницы?
                    //прячет модальное окно
                    $("#brandModal").modal('hide');
                    
                //почему не работает reset()
                    // document.getElementById('brand-brand_name').reset();
                    
                // почему не работает jquery?
                    // $("#brand-brand_name").value = '';
                },
            });
            //предотвращает стандартную отработку кнопки отправить
            return false; // prevent default submit
        });
        
        //отпрабатывает по завершению АЯКС запроса для выпадающего списка
        $('#brand-pjax' ).ajaxComplete(function() 
        {
            //определяет последний ИД в выпадающем списке
            var newBrand = $('#product-brand_id > option:last-child').val();
            //делает выбранным новый ИД бренда в выпадающем списке
            $('#product-brand_id').val(newBrand);
            
            // передаёт значение нового бренда (ИД) ПОСТ запросом
            $.ajax({
                // async: false,
                url: '/admin/product/default/create',
                type: 'POST',
                data: {newBrand: newBrand},
            });
            
        });
JS;

$this->registerJs($js);
?>
