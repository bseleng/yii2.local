<?php
use yii\widgets\DetailView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
?>
<!--модальное окно из бутстрап-->
<?php $formBrand = ActiveForm::begin([
    'id' => 'brand-form',
    'options' => ['class' => 'form-horizontal'],
]); ?>
<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<!-- Trigger the modal with a button -->
<?= Html::a('Добавить бренд',
    ['#',], [
            'class' => 'btn btn-info btn-lg',
            'data-toggle' => 'modal',
            'data-target'=> '#brandModal'
    ])
?>
<!-- Modal -->
<div id="brandModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
<!--        ['/admin/brand/default/update',]-->
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<?php ActiveForm::end(); ?>
<!--Yii2 activeForm AJAX send-->

<?php $form = ActiveForm::begin([
    'id' => 'product-form',
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

            <div class="input-group">
                <!--  Выпадающий список бренд продукта           -->
                <?= $form->field($modelProduct, 'brand_id')
                    ->dropDownList(
                        ArrayHelper::map(\app\models\Brand::find()
                            ->all(),
                            'brand_id',
                            'brand_name'))
                    ->hint('Бренд продукта, который видит конечный пользователь')
                    ->label('Бренд продукта')
                ?>
                <!--Кнопка добавления бренда-->
                <span class="input-group-btn">
                    <?php
                    Modal::begin([
                        'header' => '<h2>Введите название нового бренда</h2>',
                        'toggleButton' => [
                            'label' => 'Добавить бренд',
                            'class' => 'btn btn-outline-secondary',
                            'style' => '
                                margin-bottom:1rem;
                                font-size: 1.4rem;
                                border-radius: 0 0.3rem 0.3rem 0;
                            '
                        ],
                        'footer' => 'добавление бренда',
//                        'url' => Url::to(['/admin/product/default/create']),
                    ]);


                    Modal::end();
                    ?>
                </span>
            </div>

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
            <?= $form->field($modelProduct, 'image_path')->fileInput(); ?>
        </div>
    </div>
    <!--Кнопка сохранения изменений в модель    -->
    <?= Html::submitButton('Сохранить', [
            'class' => 'btn btn-primary',
            'style'=> 'float:right; margin:1rem;',
            'name' => 'SaveBtn',
            'value' => 'SaveBtn',
    ]) ?>
    <?= Html::submitButton('Сохранить и закрыть', [
            'class' => 'btn btn-primary',
            'style'=> 'float:right;  margin:1rem;',
            'name' => 'SaveExitBtn',
            'value' => 'SaveExitBtn',
//        url
    ]) ?>
</form>

<?php ActiveForm::end(); ?>
