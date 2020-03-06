<?php

namespace app\modules\admin\modules\product\controllers;

use app\modules\admin\modules\product\models\UploadFile;
use app\modules\admin\modules\product\models\ProductSearchForm;
use yii\web\UploadedFile;
use yii\web\Controller;
use app\modules\admin\modules\product\models\ProductForm;
use app\modules\admin\modules\product\models\BrandForm;
use yii\filters\AccessControl;
use Yii;
use yii\db\Transaction;

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
        $model = new ProductSearchForm;
        $request = Yii::$app->request;
        $model->load($request->get());
        return $this->render('index', ['model' => $model]);


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
        $modelProductForm = ProductForm::find()->where('product_id = :id', [':id' => $id])->one();

        $request = Yii::$app->request;
        if ($modelProductForm->load($request->post())) {
            $modelProductForm->imageFile = UploadedFile::getInstance($modelProductForm, 'imageFile');
            if ($modelProductForm->save()) {
                //загрузка изображения
                if ($modelProductForm->imageFile) {
                    $modelProductForm->uploadImage($modelProductForm->brand->brand_name, $modelProductForm->getPrimaryKey());
                    $modelProductForm->updateAttributes(['image_path' => $modelProductForm->constructFileName($modelProductForm->getPrimaryKey())]);
                }
            }
            //если передан ЕХИТ то редирект $_GET[]
            if ($request->post('SaveExitBtn')) {
                $this->redirect(['index']);
            }
        }

        return $this->render(
            'update',
            [
                'modelProductForm' => $modelProductForm,
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
        $modelProductForm = new ProductForm;

//ЭКСПОРТ
//php EXCEL
        $request = Yii::$app->request;
        if ($modelProductForm->load($request->post())) {
            $modelProductForm->imageFile = UploadedFile::getInstance($modelProductForm, 'imageFile');
            if ($modelProductForm->save()) {
                //загрузка изображения
                if ($modelProductForm->imageFile) {
                    $modelProductForm->uploadImage($modelProductForm->brand->brand_name, $modelProductForm->getPrimaryKey());
                    $modelProductForm->updateAttributes(['image_path' => $modelProductForm->constructFileName($modelProductForm->getPrimaryKey())]);
                }
            }
            //если передан ЕХИТ то редирект $_GET[]
            if ($request->post('SaveExitBtn')) {
                $this->redirect(['index']);
            }
        }

        return $this->render(
            'update',
            [
                'modelProductForm' => $modelProductForm,
            ]
        );
    }



    //удаление записи с указанным ИД
    public function actionDelete($id)
    {
        $modelProductForm = ProductForm::find()->where('product_id = :id', [':id' => $id])->one();
        $modelProductForm->delete();
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
        $modelBrandForm = new BrandForm;
        $request = Yii::$app->request;
        if ($modelBrandForm->load($request->post())) {
            $modelBrandForm->save();
        }


        return $this->renderAjax(
            'create_brand',
            [
                'modelBrandForm' => $modelBrandForm,
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

