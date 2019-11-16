<?php
use yii\helpers\Html;
use app\components\Helper;
?>

<div class="product-wrapper">
    <div class="load">
        <div class="bs-load"> Loading...
            <div class="bs-loader"> </div>
        </div>
    </div>

    <div class="bs-no-product-message">
        <?=$modelProductSearch->noProductFound()?>
    </div>

    <?php
    /*
    по умолчанию display:none
    по отработке условия -  display:show
    анимация загрузки на ф-и .ajax
    pjax
    сокращенный ready: $(function() {});
    */

    //записываем ГЕТ параметры (бренд, направление сортировки) в ДЖЕЙСОН
    $productSearchParams = json_encode($_GET);

    $js = <<<JS
    $(function() 
    {
        //загружает пачку товаров  с текущего нзначения страницы (pageValue)
        // и затем увеличивает значение pageValue   
        function loadGoods()
        {   
            // все параметры ($_GET+pageValue)            
            var allParams = $.extend({},{$productSearchParams}, {stranichka: pageValue});
            
            
            var request = $.ajax({
                method: "get",
                url: "/shop/ajax-get-page",
                async: false,
                data: allParams,
                dataType: "json",
            });
                
            //присоединяет данные с запрошенной страницы(урл+гет параметр) 
            //в указанный  div в формате  JSON
            request.done(function(r) 
            {
                //добавляет карточки
                $(".product-wrapper").append(r.cards);
                //скрывает индикатор загрузки
                $(".load").hide();
                
                if ((r.cards)==null)
                    $(".bs-no-product-message").show();
                
                if (r.isPage === true) {
                    //увеличивает значение текущего номера страницы на 1
                    pageValue =  parseInt(pageValue)+1;
                    
                    //показывает кнопку ЕЩЁ
                    $("#btn-load-more").show();
                } else  {
                    //скрывает кнопку ЕЩЁ
                    $("#btn-load-more").hide(1); 
                }                  
            });
        };
    
        //текущий номер страницы
        var pageValue = $("#btn-load-more").attr("page-value");
        
        //возвращает начальный набор карточек при загрузке
        loadGoods();
    
        // отрабатывает по клику кнопки с указанным ID
        //добавляет следующий набор карточек
        $("#btn-load-more").on("click", function()
        { 
            loadGoods();
        });
        
        //скрывает лицевой контент и показывает оборотную строну
        // при клике на ИНФО
        $(".product-wrapper").on('click',".info-button", function()
        {
            $(this).closest("#face-content").hide();
            $(this).closest(".bs-product-card").find("#back-content").show();
        }) ;
        
        //скрывает оборотную строну и показывает  лицевой контент
        // при клике на НАЗАД
        $(".product-wrapper").on('click',".back-button", function()
        {
            $(this).closest("#back-content").hide();
            $(this).closest(".bs-product-card").find("#face-content").show();
        });
        
        $(".product-wrapper").on('click',".buy-button", function()
        {
            var productId = $(this).attr('product-id');
            // var quantity = parseInt($(this).attr('quantity'));
            var quantity = $(this).attr('quantity');
            
            $.ajax(
            {
                method: "post",
                async: false,
                url: "/shop/ajax-shopping-cart-add",
                data: {
                    "productId": productId,
                    "quantity": quantity,
                    },
            });
            
            $(this).attr('quantity', parseInt(quantity)+1);
            consoleText = productId + " " + quantity + " шт.";
            console.log(consoleText);
            // console.log(quantity);
            
            $.ajax(
            {
                method: "get",
                async: false,
                url: "/shop/ajax-shopping-cart-show",
                dataType: "json",
            })
            .done(function( r )
            {
                $(".bs-page-stats-count").text(r.productCount);
                // console.log(r);
            });
        });
        
        $(".bs-page-stats").on('click',"#btn-shopping-cart-clear", function()
        {
             $.ajax(
            {
                method: "post",
                url: "/shop/ajax-shopping-cart-clear",
            })
        });
    });    
        
JS;

    $this->registerJs($js);

    ?>




</div>

<div class="load-more">

  <?= Html::a(Html::encode('Ещё', true), null,
    [
        'class' => 'btn btn-light btn-no-corners',
        'style' => ['display' => 'none'],
        'id' => 'btn-load-more',
        'page-value' => 1,
        'role' => 'button',
    ]);
  ?>

</div>


<div class='bs-page-stats'>

  <div class='bs-page-stats-count'></div>
  <div class='bs-page-stats-sum'> На сумму: 111890 руб.</div>

  <?= Html::a(Html::encode('Очистить корзину', true), null,
    [
        'class' => 'btn btn-light btn-no-corners',
        'id' => 'btn-shopping-cart-clear',
    ]);
  ?>

</div>