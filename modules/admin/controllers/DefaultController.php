<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;


class DefaultController extends Controller
{


    //открывает стартовую страницу
    public function actionIndex()
    {
        return $this->render('index');
    }

    //открывает страницу входа в учётную запись
    //даёт возможность войти в учётную запись
    public function actionLogin()
    {
        if (!Yii::$app->userAdmin->isGuest) {
            return $this->redirect(['index']);
        }

        $model = new \app\modules\admin\models\LoginForm();
        # НЕ ПОНИМАЮ что значит эта строка
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/admin/product/default/index']);
        }
        # НЕ ПОНИМАЮ весь блок
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */

    //производит выход из учётной записи
    public function actionLogout()
    {
        Yii::$app->userAdmin->logout();
        return $this->redirect(['login']);
    }

    //открывает страницу с информацией
    public function actionInfo()
    {
        return $this->render('info');
    }

    //открывает страницк контактов
    public function actionContact()
    {
        return $this->render('contact');
    }

}

