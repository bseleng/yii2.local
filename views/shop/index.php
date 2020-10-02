<?php
/**
 * Created by PhpStorm.
 * User: Bogdan S
 * Date: 08.08.2019
 * Time: 17:34
 */

use yii\helpers\Html;
use yii\base\View;
?>

<div class="bs-products-area">

<?php

//echo '<pre>';
//var_dump($_GET);
//echo '</pre>';


// выводим товар попадающий под фильтр
echo $this->render('_filter_bar', ['modelProductSearch'=> $modelProductSearch]);
echo $this->render(
    '_product',
    [
        'modelProductSearch' => $modelProductSearch,
        'modelProduct' => $modelProduct,
        'modelShoppingCart' => $modelShoppingCart,
    ]
);

//$this->params['brandSort'][] = $modelProductSearch->brand_id;
//var_dump($this->params['brandSort']);

?>

</div>
