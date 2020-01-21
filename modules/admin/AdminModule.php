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

     //Длинная перенастройка страницы входа в учётную запись, подсмотрел на оф форуме
/*     Yii::$app->set('user', [
         'class' => 'yii\web\User',
         'identityClass' => 'app\modules\admin\models\UserAdmin',
         'enableAutoLogin' => true,
         'loginUrl' => ['/admin/default/login'],
     ]);*/
 }
}