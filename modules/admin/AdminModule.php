<?php
namespace app\modules\admin;

use Yii;

class AdminModule extends \yii\base\Module
{
 public function init()
 {
     parent::init();
     //выбор шаблона для модуля
     $this->layout = 'adminLayout';
     //Короткая настройка страницы входа в учётную запись
     Yii::$app->user->loginUrl = ['admin/default/login'];
 }
}