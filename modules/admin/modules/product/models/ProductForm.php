<?php

namespace app\modules\admin\modules\product\models;

use \app\models\Product;
use yii\helpers\FileHelper;


class ProductForm extends Product
{
    public $imageFile;
    public $product_id_text;

    public function rules()
    {
        return [
            [['product_name', 'product_description', 'image_path',], 'string'],
            [['price_base', 'price_discounted',], 'number'],
            [['brand_id', 'product_id',], 'integer'],
            //чтоб избежать ошибку, когда нет цены
            [['price_base', 'price_discounted',], 'default', 'value' => 0],
            [['image_path',], 'default', 'value' => ''],
            // необходимые поля
            [['product_name', 'brand_id',], 'required'],
            //изображение
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * собирает имя файла в формате ИД_ПРОДУКТА.РАСШИРЕНИЕ
     *
     * @param string $id  ИД продукта
     * @return string string имя файла в формате ИД_ПРОДУКТА.РАСШИРЕНИЕ
     */
    public function  constructFileName($id)
    {
        return $id . '.' . $this->imageFile->extension;
    }

    /**
     * возвращает директорию для сохранения,
     * создаёт, если такой не было
     *
     * @param string $brand  бренд продукта
     * @return string
     * @throws \yii\base\Exception
     */
    public function getDir($brand)
    {
        $path = 'uploads/shop/pic/' .  $brand . '/';
        if (!is_dir($path)) {
            FileHelper::createDirectory($path);
        }

        return $path;
    }

    /**
     * сохраняет файл в директорию, согласно указанного пользователем бренда
     *
     * @param string $brand  бренд продукта
     * @param string $id  ИД продукта
     * @return bool
     * @throws \yii\base\Exception
     */
    public function uploadImage($brand, $id)
    {
        if ($this->validate()) {
            $this->imageFile->saveAs($this->getDir($brand) . $this->constructFileName($id));
            return true;
        } else {
            return false;
        }
    }


}

