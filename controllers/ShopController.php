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

        // создаёт объект зароса
        $request = Yii::$app->request;
        // получает ИД продукта (согласно БД) и отправляет пост запросом
        $productId = $request->post('productId');
        // получает количество продукта и отправляет пост запросом
        $quantity = $quantity = $request->post('quantity');

        // получение коллекции кук (yii\web\CookieCollection) из компонента "request"
        $cookies = Yii::$app->request->cookies;

        //создаёт модель корзины ShoppingCart
        $modelShoppingCart = new ShoppingCart();

        // получает массив состава заказа из кук и передаёт его в модель
         $modelShoppingCart->orderArr = $cookies->getValue('order');

        // добавляет товары в массив заказа на основании данных пост запроса
        $order = $modelShoppingCart->addProduct($productId, $quantity);

        // получение коллекции (yii\web\CookieCollection) из компонента "response"
        $cookies = Yii::$app->response->cookies;
        // добавление новой куки в HTTP-ответ
        $cookies->add(new \yii\web\Cookie([
            'name' => 'order',
            'value' => $order,

        ]));

        return json_encode($cookies->getValue('order'));
    }

    public function actionAjaxShoppingCartShow()
    {
        // получение коллекции кук (yii\web\CookieCollection) из компонента "request"
        $cookies = Yii::$app->request->cookies;

        //создаёт модель корзины ShoppingCart
        $modelShoppingCart = new ShoppingCart();
        // получает массив состава заказа из сессии и передаёт его в модель
        $modelShoppingCart->orderArr = $cookies->getValue('order');

       // return json_encode($modelShoppingCart->getCartProductCount());
        return $modelShoppingCart->getShoppingCartValues();
    }

    public function actionAjaxShoppingCartClear()
    {

        // получение коллекции (yii\web\CookieCollection) из компонента "response"
        $cookies = Yii::$app->response->cookies;

        // удаление куки...
        return  $cookies->remove('order');

    }
}


