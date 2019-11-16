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
        
        //Набор действий при нажатии на кнопку купить
        // 0) инкремент количества продукта, кнопку которого прожал пользователь
        // 1) пост запос - передача на экшон добавления количества продуктов для каждого ИД
        // 2) гет запрос - получение с экноша показа данных о количестве и ид продуктов
        // 2.1) замена текста в целевом диве полученными данными
        //
        
        $(".product-wrapper").on('click',".buy-button", function()
        {
            // ИД продукта (текстовое значение)
            var productId = $(this).attr('product-id');
            // Количество продукта (текстовое значение)
            var quantity = $(this).attr('quantity');
            
            //запрос на экшон добавления текущего состава заказа
            $.ajax(
            {
                method: "get",
                async: false,
                url: "/shop/ajax-shopping-cart-add",
            })
            .done(function(r)
            {
                var parsed = JSON.parse(r);
                // console.log($(this).attr('quantity', parseInt(quantity)+1));
                
                //эта часть должна сохранять состав заказа после обновления страницы
                $.each( parsed, function( key, value ) {
                    var test = value.productId + " - " + value.quantity + " шт. после обновления";
                    console.log(test);
                    $('[product-id="'+value.productId+'"]')
                        .closest("div")
                        .find(":button")
                        .attr('quantity', parseInt(value.quantity));  
                });                 
            });
            
            
            //Икремент количества продукта, кнопку купить которого, нажал пользователь
            $(this).attr('quantity', parseInt(quantity)+1);
            
            
            //отправка на экшон добавления ИД и Количества продукта
            $.ajax(
            {
                method: "post",
                async: false,
                url: "/shop/ajax-shopping-cart-add",
                data: {
                    "productId": productId,
                    "quantity": quantity,
                },
            })
            //отладка - вывод в консоль
            .done(function( r )
            {
                consoleText = productId + " - " + quantity + " шт.";
                console.log(consoleText);
                // console.log(r);
                // console.log(quantity);
            });

            // получение с экшон отображения ИД и Количества продукта
            $.ajax(
            {
                method: "get",
                async: false,
                url: "/shop/ajax-shopping-cart-show",
                dataType: "json",
            })
            //замена текста целевого дива полученными данными
            .done(function( r )
            {
                $(".bs-page-stats-count").text(r.productCount);
            });
        });
        
        //очистка корзины путём удаления сессии
        //удалить, перезагрузить - пока для отладки
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