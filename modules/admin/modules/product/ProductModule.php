<?php

namespace app\modules\admin\modules\product;

class ProductModule extends \yii\base\Module
{
    public function init()
    {
        parent::init();

        $this->params['foo'] = 'bar';
        // ... остальной инициализирующий код ...
}
}