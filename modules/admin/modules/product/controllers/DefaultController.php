<?php

namespace app\modules\admin\modules\product\controllers;

use app\modules\admin\modules\product\models\UploadFile;
use yii\web\UploadedFile;
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
     *      если передан - находит соответствующий товар в БД по ИД
     * проверяет данные ПОСТ запроса
     *      если запрос не пустой - сохраняет данные в модель
     *      если запрос содержит значение кнопки СОХРАНИТЬ И ВЫЙТИ
     *          - возвращает в стандартное представление модуля ПРОДУКТ (список)
     *
     * @param int $id идентификатор товара из БД
     * @return string представление редактирования товара
     */
    public function actionUpdate($id)
    {
        $modelProduct = Product::find()->where('product_id = :id', [':id' => $id])->one();

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

    /**
     * открывает представление создания товара
     *
     * проверяет данные ПОСТ запроса
     *      если запрос не пустой - сохраняет данные в модель
     *      если запрос содержит значение кнопки СОХРАНИТЬ И ВЫЙТИ
     *          - возвращает в стандартное представление модуля ПРОДУКТ (список)
     *
     * @return string представление список товаров
     */
    public function actionCreate()
    {
        $modelProduct = new Product;

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

    /**
     * открывает представление добавления бренда в модальном окне
     *
     * проверяет данные ПОСТ запроса, если не пустой - сохраняет данные в модель
     *
     * @return string модальное окно добавления бренда
     */
    public function  actionCreateBrand()
    {
        $modelBrand = new Brand;
        $request = Yii::$app->request;
        if ($modelBrand->load($request->post())) {
            $modelBrand->save();
        }

        return $this->renderAjax(
            'create_brand',
            [
                'modelBrand' => $modelBrand,
            ]
        );

    }

    //стандартное действие загрузки файла из документации
    public function actionUpload()
    {
        $model = new UploadFile();

        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                // file is uploaded successfully
                $this->redirect(['upload']);
                return;
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

}

