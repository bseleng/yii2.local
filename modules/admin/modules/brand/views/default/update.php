<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<h3> Редактировать название Бренда</h3>

<!--Текстовый ввод для бренда-->
<?=$this->render('_brand_form', ['modelBrand' => $modelBrand,]); ?>