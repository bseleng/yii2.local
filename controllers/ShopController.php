<?php

namespace app\controllers;

use app\components\Helper;
use Yii;
use yii\web\Controller;
use app\models\ProductSearch;


class ShopController extends Controller
{
    // выводимое количество товаров
    public $limit = 4;

    public function actionIndex()
    {

        $modelProductSearch = new ProductSearch;
        //$modelProductSearch->load(Yii::$app->request->post());
        $modelProductSearch->load(Yii::$app->request->get());
//        var_dump($modelProductSearch->productNameSort);
//        var_dump($modelProductSearch->brand_id);



         $mainPageContent = $this->render(
            'index',
            [
            'modelProductSearch'=>$modelProductSearch,
            ]
        );

        return $mainPageContent;

    }


    public function  actionAjaxGetPage()
    {
        $stranichka = Yii::$app->request->get('stranichka');
        $offset = Helper::getOffset($this->limit, $stranichka);

        $modelProductSearch = new ProductSearch;
        $modelProductSearch->load(Yii::$app->request->get());

        $arrModelProduct = $modelProductSearch->search($offset, $this->limit);

        foreach ($arrModelProduct as $modelProduct) {
            $newCards[] =($this->renderPartial(
                '_product_card',
                [
                    'modelProduct'=>$modelProduct,

                ])
            );
        }

        $isPage = $modelProductSearch->findLastPage($this->limit, $stranichka);

        return json_encode([
            'isPage'=>$isPage,
            'cards'=>$newCards,
            'stranichka'=>$stranichka,
        ]);
    }

    public function actionAjaxGetDescription()
    {

    }
}


