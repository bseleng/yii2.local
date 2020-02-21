<?php

namespace app\modules\admin\modules\brand\controllers;

use yii\web\Controller;
use app\modules\admin\modules\brand\models\BrandForm;
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
     * открывает представление редактирования бренда
     *
     * проверяет $id,
     *      если не передан - устанавливает new по умолчанию
     *      если передан - находит соответствующий бренд в БД по ИД
     * проверяет данные ПОСТ запроса
     *      если запрос не пустой - сохраняет данные в модель
     *
     * @param string|int $id идентификатор товара из БД, либо слово new для создания нового
     * @return string представление редактирования товара
     */
    public function actionUpdate($id)
    {
        $modelBrandForm = BrandForm::find()->where('brand_id = :id', [':id' => $id])->one();

        $request = Yii::$app->request;
        if ($modelBrandForm->load($request->post())) {
            $modelBrandForm->save();
            $this->redirect(['index']);
        }
        return $this->render(
            'update',
            [
                'modelBrandForm' => $modelBrandForm,
            ]
        );
    }

    //удаление записи с указанным ИД
    public function actionDelete($id)
    {
        $modelBrandForm = BrandForm::find()->where('brand_id = :id', [':id' => $id])->one();
        $modelBrandForm->delete();
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
    }

    public function actionCreate()
    {
        $modelBrandForm= new BrandForm;

        $request = Yii::$app->request;
        if ($modelBrandForm->load($request->post())) {
            $modelBrandForm->save();
            $this->redirect(['index']);
        }
        return $this->render(
            'create',
            [
                'modelBrandForm' => $modelBrandForm,
            ]
        );
    }



}

