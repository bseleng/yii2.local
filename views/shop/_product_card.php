<?php
use yii\helpers\Html;
use yii\base\View;
?>

<!-- Карточка продукта-->
<div class="bs-product-card">

    <!-- Лицевая сторона карточки-->
    <div id="face-content">
        <!-- Бренд продукта (карточка)-->
        <div class='bs-product-brand'>
            <?= $modelProduct->brand->brand_name; ?>
        </div>

        <!-- Наименование продукта (карточка)-->
        <div class=<?= $modelProduct->selectNameStyle(10);?>>
            <?= $modelProduct->product_name; ?>
        </div>

        <!-- Изобрадение продукта (карточка)-->
        <img class="bs-product-image" src="<?= $modelProduct->findImagePath() ?>">  </img>

        <!-- Див с кнопками КУПИТЬ и ИНФО-->
        <div class='bs-product-buttons'>
            <?=
            Html::button(
                Html::encode('Купить'),
                [
                    'class' => ['bs-button', 'buy-button'],
                    'product-id' => $modelProduct->product_id,
                    'quantity' => 1,
                ]);
            ?>

            <?=
            Html::button(
                Html::encode('Инфо'),
                [
                    'class' => ['bs-button','info-button']
                ]);
            ?>

        </div>


        <!-- Актуальная цена продукта (карточка)-->
        <div class=<?= $modelProduct->selectNewPriceStyle()?>>
            <?= $modelProduct->price_discounted; ?>
        </div>

        <!-- Старая цена продукта (до скидки) (карточка)   -->
        <div class=<?= $modelProduct->selectOldPriceStyle()?>>
            <?= $modelProduct->price_base; ?>
        </div>

    </div>



    <!--    Оборотная сторона карточки-->
    <div class="bs-back-content" id="back-content">
        <!--    Описание продукта -->
        <div class="bs-description"> <?= $modelProduct->product_description ?> </div>

        <!--   Кнопка назад (к лицевой стороне)     -->
        <div class='bs-back-button'>
            <?php
            echo Html::button(Html::encode('Назад'),
                [
                    'class' => ['bs-button', 'back-button']

                ]);
            ?>
        </div>

    </div>



</div>

<?php
$js = <<<JS
$ (function()
{
    alert('ttt');
    $(".bs-product-card").on('click',".buy-button", function() {
        alert('rrrrrrr');
    });
    
});

JS;

$this->registerJs($js);


?>
