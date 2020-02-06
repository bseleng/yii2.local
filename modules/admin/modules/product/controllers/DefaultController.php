<?php

namespace app\modules\admin\modules\product\controllers;

use yii\web\Controller;
use \app\models\Product;
use \app\models\Brand;
use yii\filters\AccessControl;
use Yii;

class DefaultController extends Controller
{
    //правила доступа (действия 'update', 'index' доступны только после входа в учётную запись)
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'index'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'index',],
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

    //открывает страницу для редактирования конкретной записи из модели

    /**
     * открывает представление редактирования товара
     *
     * проверяет $id,
     *      если не передан - устанавливает new по умолчанию
     *      если передан - находит соответствующий товар в БД по ИД
     * проверяет данные ПОСТ запроса
     *      если запрос не пустой - сохраняет данные в модель
     *      если запрос содержит значение кнопки СОХРАНИТЬ И ВЫЙТИ - возвращает в стандартное представление модуля ПРОДУКТ (список)
     *
     * @param string|int $id идентификатор товара из БД, либо слово new для создания нового
     * @return string представление редактирования товара
     */
    public function actionUpdate($id = 'new')
    {
        if ($id !== 'new') {
            $modelProduct = Product::find()->where('product_id = :id', [':id' => $id])->one();
        } else {
            $modelProduct = new Product;
        }

        $request = Yii::$app->request;
        if ($modelProduct->load($request->post())) {
            $modelProduct->save();
            //если передан ЕХИТ то редирект $_GET[]
            if ($request->post('SaveExitBtn')) {
                $this->redirect(['index']);
            }
        }

        return $this->render(
            'update',
            [
                'modelProduct' => $modelProduct,
            ]
        );
    }

    //удаление записи с указанным ИД
    public function actionDelete($id)
    {
        $modelProduct = Product::find()->where('product_id = :id', [':id' => $id])->one();
        $modelProduct->delete();
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
    }

    public function  actionCreateBrand()
    {
        $modelBrand = new Brand;
        $request = Yii::$app->request;
        if ($modelBrand->load($request->post())) {
            $modelBrand->save();
            $this->redirect(['update']);
        }

        return $this->renderPartial(
            'create_brand',
            [
                'modelBrand' => $modelBrand,
            ]
        );
    }

}

