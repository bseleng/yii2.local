<?php

namespace app\controllers;

use app\components\Helper;
use Yii;
use yii\web\Controller;
use app\models\ProductSearch;
use app\models\Product;
use app\models\ShoppingCart;
use yii\web\Session;
use yii\web\Request;
use yii\helpers\VarDumper;


class ShopController extends Controller
{
    // выводимое количество товаров
    public $limit = 4;

    public function actionIndex()
    {
        // создаёт объект сессии
        $session = Yii::$app->session;
        // открывает сессию
        $session->open();
        
        //отладка: уничтожение сессии и печать массива order
//        $session->destroy();
//        print_r($session->get('order'));

        //модная отладка
        Yii::warning('debug',  VarDumper::dumpAsString($session->get('order')));
        // или
//        Yii::warning('debug',  print_r($session->get('order'), true));

        $modelProductSearch = new ProductSearch;
        $modelProduct = new Product;
        $modelShoppingCart = new ShoppingCart();
        $modelProductSearch->load(Yii::$app->request->get());

        $mainPageContent = $this->render(
            'index',
            [
            'modelProductSearch' => $modelProductSearch,
            'modelProduct' => $modelProduct,
            'modelShoppingCart' => $modelShoppingCart,
            ]
        );

        return $mainPageContent;
    }


    public function  actionAjaxGetPage()
    {
        // создаёт объект сессии
        $session = Yii::$app->session;
        // открывает сессию
        $session->open();

        $stranichka = Yii::$app->request->get('stranichka');
        $offset = Helper::getOffset($this->limit, $stranichka);

        $modelProductSearch = new ProductSearch;
        $modelProductSearch->load(Yii::$app->request->get());

        $arrModelProduct = $modelProductSearch->search($offset, $this->limit);

        $modelShoppingCart = new ShoppingCart();



        foreach ($arrModelProduct as $modelProduct) {
            $newCards[] =($this->renderPartial(
                '_product_card',
                [
                    'modelProduct' => $modelProduct,
                    'modelShoppingCart' => $modelShoppingCart,
                ])
            );
        }

        $isPage = $modelProductSearch->findLastPage($this->limit, $stranichka);

        return json_encode([
            'isPage' => $isPage,
            'cards' => $newCards,
            'stranichka' => $stranichka,
        ]);
    }

    public function actionAjaxShoppingCartAdd()
    {
        // создаёт объект сессии
        $session = Yii::$app->session;
        // открывает сессию
        $session->open();

        // создаёт объект зароса
        $request = Yii::$app->request;
        // ИД продукта в БД
        $productId = $request->post('productId');
        // количество товара накликанное пользователем
        $quantity = $request->post('quantity');

        // получаем массив состава заказа записываем в переменную order
        $order = $session->get('order');

        // в подмассив заказа ключу $productId присваеваем
        // актуальный (последний кликнутый) ИД продукта и накликаное количество
        $order[$productId] =
            [
                'productId' => $productId,
                'quantity' => $quantity,
            ];

        // в массив сесии order записываем актуальный состав заказа
        $session->set('order',$order);

        return json_encode($order);

    }

    public function actionAjaxShoppingCartShow()
    {
        // создаёт объект сессии
        $session = Yii::$app->session;
        // открывает сессию
        $session->open();

        // получаем массив состава заказа записываем в переменную order
        $order = $session->get('order');

        $modelShoppingCart = new ShoppingCart();
        $modelShoppingCart->orderArr = $order;


//        var_dump($modelShoppingCart->getCartProductCount());

        return json_encode([
            'productCount' => $modelShoppingCart->getCartProductCount(),
//            'quantity' => $quantity,

        ]);
    }

    public function actionAjaxShoppingCartClear()
    {
        // создаёт объект сессии
        $session = Yii::$app->session;
        // открывает сессию
        $session->open();
        //уничтожает сессию
        return $session->destroy();

    }
}


