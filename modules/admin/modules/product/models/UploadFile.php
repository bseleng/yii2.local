<?php
namespace app\modules\admin\modules\product\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadFile extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs(
                'uploads/shop/pic' .
//                $brand . '/' .
                $this->imageFile->baseName .
                '.' . $this->imageFile->extension
            );
            return true;
        } else {
            return false;
        }
    }
}