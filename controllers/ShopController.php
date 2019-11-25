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

    /**
     * @return string
     */
    public function actionAjaxShoppingCartAdd()
    {
        // создаёт объект сессии
        $session = Yii::$app->session;
        // открывает сессию
        $session->open();
        // создаёт объект зароса
        $request = Yii::$app->request;

        //создаёт модель корзины ShoppingCart
        $modelShoppingCart = new ShoppingCart();
        // получает массив состава заказа из сессии и передаёт его в модель
        $modelShoppingCart->orderArr = $session->get('order');
        // получает ИД продукта (согласно БД) из сессии
        $productId = $request->post('productId');
        // получает количество продукта из сессии
        $quantity= $quantity = $request->post('quantity');
        // добавляет товары в массив заказа на основании данных пост запроса
        $order = $modelShoppingCart->addProduct($productId, $quantity);


        // записывает обновлённый массив заказа в сессию
        $session->set('order',$order);

        return json_encode($order);
    }

    public function actionAjaxShoppingCartShow()
    {
        // создаёт объект сессии
        $session = Yii::$app->session;
        // открывает сессию
        $session->open();

        //создаёт модель корзины ShoppingCart
        $modelShoppingCart = new ShoppingCart();
        // получает массив состава заказа из сессии и передаёт его в модель
        $modelShoppingCart->orderArr = $session->get('order');

       // return json_encode($modelShoppingCart->getCartProductCount());
        return $modelShoppingCart->getShoppingCartValues();
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


