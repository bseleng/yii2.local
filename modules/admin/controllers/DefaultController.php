<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;
//use app\modules\admin\models\LoginForm;

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
        $this->layout = 'main2';
        if (!Yii::$app->userAdmin->isGuest) {
            return $this->redirect(['index']);
        }

        $model = new \app\modules\admin\models\LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['secret']);
        }

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
        $this->layout = 'main2';
        Yii::$app->userAdmin->logout();

        return $this->redirect(['login']);
    }

    //открывает страницу для пользвателей
    public function actionSecret()
    {
        return $this->render('secret');
    }

    public function actionInfo()
    {
        return $this->render('info');
    }

    public function actionContact()
    {
        return $this->render('contact');
    }

}

