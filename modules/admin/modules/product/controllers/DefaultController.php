<?php

namespace app\modules\admin\modules\product\controllers;

use app\modules\admin\modules\product\models\BrandForm;
use app\modules\admin\modules\product\models\Export;
use app\modules\admin\modules\product\models\ProductForm;
use app\modules\admin\modules\product\models\ProductSearchForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\UploadedFile;

class DefaultController extends Controller
{
    /**
     * задаёт правила доступа (действия 'update', 'index' доступны только после входа в учётную запись)
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['update', 'index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'index', 'create', 'update', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * открывает стартовую страницу
     *
     * @return string
     */
    public function actionIndex()
    {
        $modelProductSearchForm = new ProductSearchForm;

        $request = Yii::$app->request;
        $modelProductSearchForm->load($request->get());
        Url::remember();

        return $this->render('index', [
            'modelProductSearchForm' => $modelProductSearchForm,
        ]);
    }


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
                    $modelProductForm->uploadImage($modelProductForm->brand->brand_name,
                        $modelProductForm->getPrimaryKey());
                    $modelProductForm->updateAttributes(['image_path' => $modelProductForm->constructFileName($modelProductForm->getPrimaryKey())]);
                }
            }
            //если передан SaveExitBtn то редирект на предыдущую страницу
            if ($request->post('SaveExitBtn')) {
                $this->redirect(Url::previous());
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
     * @return string
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $modelProductForm = new ProductForm;

        $request = Yii::$app->request;
        if ($modelProductForm->load($request->post())) {
            $modelProductForm->imageFile = UploadedFile::getInstance($modelProductForm, 'imageFile');
            if ($modelProductForm->save()) {
                //загрузка изображения
                if ($modelProductForm->imageFile) {
                    $modelProductForm->uploadImage($modelProductForm->brand->brand_name,
                        $modelProductForm->getPrimaryKey());
                    $modelProductForm->updateAttributes(['image_path' => $modelProductForm->constructFileName($modelProductForm->getPrimaryKey())]);
                }
            }
            //если передан SaveExitBtn то редирект на предыдущую страницу
            if ($request->post('SaveExitBtn')) {
                $this->redirect(Url::previous());
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
     * удаление записи с указанным ИД
     *
     * @param int $id ИД товара, который нужно удалить
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $modelProductForm = ProductForm::find()->where('product_id = :id', [':id' => $id])->one();
        if ($modelProductForm->image_path) {
            unlink($modelProductForm->getDir($modelProductForm->brand->brand_name) . $modelProductForm->image_path);
        }
        $modelProductForm->delete();
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(['index']);
        }
    }

    /**
     * открывает представление добавления бренда в модальном окне
     * проверяет данные ПОСТ запроса, если не пустой - сохраняет данные в модель
     *
     * @return string модальное окно добавления бренда
     */
    public function actionCreateBrand()
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

    /**
     * выводит .XLSX файл с подборкой отфильрованных товаров в бразуер
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExportXlsx()
    {
        $modelProductSearchForm = new ProductSearchForm;
        $request = Yii::$app->request;
        $modelProductSearchForm->load(json_decode($request->get('getParams'), true));
        $dataProvider = $modelProductSearchForm->search();

        $modelExport = new Export();
        //вывод .XLS файла отфильтрованной подборки в браузер
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . 'XLSX-shop-' . date('d-m-y__Hi') . '.xlsx');
        header('Cache-Control: max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($modelExport->export($dataProvider), 'Xls');
        $writer->save('php://output');
    }

    /**
     * выводит .CSV файл с подборкой отфильрованных товаров в бразуер
     */
    public function actionExportCsv()
    {
        $modelProductSearchForm = new ProductSearchForm;
        $request = Yii::$app->request;
        $modelProductSearchForm->load(json_decode($request->get('getParams'), true));
        $dataProvider = $modelProductSearchForm->search();

        $modelExport = new Export();
        $file = $modelExport->writeToFile($dataProvider);
        //вывод .CSV файла отфильтрованной подборки в браузер
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . 'CSV-shop-' . date('d-m-y__Hi') . '.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        echo $file;
        exit;
    }

}