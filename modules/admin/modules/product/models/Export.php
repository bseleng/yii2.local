<?php
namespace app\modules\admin\modules\product\models;
require '../../../../vendor/autoload.php';

use yii\base\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends Model
{
    public function export()
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="file.xls"');

        /** Create a new Spreadsheet Object **/
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');
        if (file_exists($writer)) {

            Yii::$app->response->sendFile($writer);

        } ;
    }
}