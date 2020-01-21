<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use Yii;
use \app\models\Product;
use yii\filters\AccessControl;

class DefaultController extends Controller
{
    //правила доступа
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'secret'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'secret',],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    //открывает стартовую страницу
    public function actionIndex()
    {
        return $this->render('index');
    }

    //открывает страницу входа в учётную запись
    //даёт возможность войти в учётную запись
    # НЕПОНЯТНО как её сделать недоступной для гостя
    public function actionLogin()
    {
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
        Yii::$app->userAdmin->logout();

        return $this->redirect(['login']);
    }

    //открывает страницу для пользвателей выполнивших вход (на данный момент страница для ГридВью)
    public function actionSecret()
    {
        $modelProduct = new \app\models\Product;
        return $this->render('secret', [
            'modelProduct' => $modelProduct,
        ]);
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
    //открывает страницу для редактирования конкретной записи из модели
    public function actionUpdate($id)
    {
        $modelProduct = Product::find()->where('product_id = :id', [':id' => $id])->one();
        if ($modelProduct->load(\Yii::$app->request->post())) {
            $modelProduct->save();
        }
        return $this->render(
            'update',
            [
                'modelProduct' => $modelProduct,
            ]
        );
    }

    public function actionCreate()
    {
        $modelProduct = new Product;
        if ($modelProduct->load(\Yii::$app->request->post())) {
            $modelProduct->save();
        }
        return $this->render(
            'update',
            [
                'modelProduct' => $modelProduct,
            ]
        );
    }
    public function actionDelete($id)
    {
        $modelProduct = Product::find()->where('product_id = :id', [':id' => $id])->one();
        $modelProduct->delete();
        if ($modelProduct->load(\Yii::$app->request->post())) {
            $modelProduct->save();
        }
        return $this->render(
            'secret',
            [
                'modelProduct' => $modelProduct,
            ]
        );
    }

}

