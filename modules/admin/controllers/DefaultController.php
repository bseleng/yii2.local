<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;


class DefaultController extends Controller
{
    /**
     * открывает страницу входа в учётную запись
     * даёт возможность войти в учётную запись
     *
     * @return string|\yii\web\Response
     */

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/admin/product/default/index']);
        }

        $model = new \app\modules\admin\models\LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/admin/product/default/index']);
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
        Yii::$app->userAdmin->logout();
        return $this->redirect(['login']);
    }

}

