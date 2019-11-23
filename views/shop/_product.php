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
        //проверяет состав заказа
        getActualOrder();
        
    
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
        
        //Набор действий при нажатии на кнопку КУПИТЬ
        // 1) пост запрос - передача на экшон добавления массива с составом заказа
        // 2) гет запрос - получение с экноша показа данных об общем количестве продуктов
        // 2.1) замена текста в целевом диве полученными данными
        //
        
        $(".product-wrapper").on('click',".buy-button", function()
        {
            // ИД продукта (текстовое значение)
            var productId = $(this).attr('product-id');
            // Количество продукта (текстовое значение)
            var quantity = $(this).attr('quantity');
            
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
            });
            
            //показать актуальное состояние корзины  
            getActualOrder();
           
        });
        
        //обновляет значение заказа в виджете корзины
        function getActualOrder()
        {
             // получение с экшон отображения ИД и Количества продукта
             // проверка наличия состава заказа и скрытие/показ дива с заказом
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
                $(".bs-page-stats-count").text(r.productTotalCount);
                $(".bs-page-stats-sum").text(r.productTotalSum);
            });
            
            // console.log($(".bs-page-stats-sum").is(':empty'));           
            if($(".bs-page-stats-count").text() == 0)
                $('.bs-page-stats').hide();
            else
                $('.bs-page-stats').show();
        }
        
        //очистка корзины путём удаления сессии
        //удалить, перезагрузить - пока для отладки
        $(".bs-page-stats").on('click',"#btn-shopping-cart-clear", function()
        {
             $.ajax(
            {
                method: "post",
                async: false,
                url: "/shop/ajax-shopping-cart-clear",
            });
            
            //показать актуальное состояние корзины    
            getActualOrder();
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

  <div>
      <span class='bs-page-stats-count-text'> <?=$modelShoppingCart->productCountText?></span>
      <span class='bs-page-stats-count'></span>
  </div>

  <div>
      <span class='bs-page-stats-sum-text'><?=$modelShoppingCart->productPriceText?> </span>
      <span class='bs-page-stats-sum'></span>
  </div>

  <?= Html::a(Html::encode('Очистить корзину', true), null,
    [
        'class' => 'btn btn-light btn-no-corners',
        'id' => 'btn-shopping-cart-clear',
    ]);
  ?>

</div>