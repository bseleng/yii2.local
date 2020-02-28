<?php

namespace app\modules\admin\modules\product\models;

use \app\models\Brand;

class BrandForm extends Brand
{
    public function rules()
    {
        return [
            [['brand_name'], 'string'],
        ];
    }


}

