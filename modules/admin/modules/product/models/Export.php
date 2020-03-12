<?php
namespace app\modules\admin\modules\product\models;
//require '../../../../vendor/autoload.php';

use yii\base\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

use yii\data\DataProviderInterface;
use yii\data\ActiveDataProvider;

class Export extends ProductSearchForm
{

    /**
     * Устанавливает оффсет по оси X для расположения изображения по центру
     *
     * @param object $drawing объект изображения PhpOffice\PhpSpreadsheet\Worksheet\Drawing
     * @param int $colWidth ширина столбца, где хранится изображение В ЕДИНИЦАХ  EXCEL
     * @return mixed объект изображения с установленным свойством горизонтального оффсета по середине столбца
     */
    private function centerDrawingHorizontal($drawing, $colWidth)
    {
        //not defined which means we have the standard width
        if ($colWidth == -1) {
            //pixels, this is the standard width of an Excel cell in pixels = 9.140625 char units outer size
            $colWidthPixels = 64;
        } else {
            //innner width is 8.43 char units
            //colwidht in Char Units * Pixels per CharUnit
            $colWidthPixels = $colWidth * 7.0017094;
        }
        //если изображение шире колонки, сужаем его
        if ($colWidthPixels < $drawing->getWidth()) {
            $drawing->setWidth($colWidthPixels*0.9);
        }
        //pixels
        $offsetX = ($colWidthPixels - $drawing->getWidth())/2;
        //pixels
        return $drawing->setOffsetX($offsetX);
    }

    /**
     * Устанавливает оффсет по оси Y для расположения изображения по центру
     * @param object $drawing объект изображения PhpOffice\PhpSpreadsheet\Worksheet\Drawing
     * @param int $rowHeight высота ряда
     * @return mixed объект изображения с установленным свойством вертикального оффсета по середине столбца
     */
    private function centerDrawingVertical($drawing, $rowHeight)
    {
        $offsetY = ($rowHeight - $drawing->getHeight());
        return $drawing->setOffsetY($offsetY);

    }

    /**
     * записывает выбранную пользователем подборку с первой страницы в .XLS
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export()
    {
        //Создаёт объект книги
        $spreadsheet = new Spreadsheet();
        //Устанавливает акивный лист
        $sheet = $spreadsheet->getActiveSheet();
        //Получает массив моделей на ПЕРВОМ листе подборки
        $collection = $this->search()->getModels();
        //Получает количество моделей на ПЕРВОМ листе подборки
        $count = $this->search()->getCount();

        //счётчик для прохода по массиву с моделями
        $i = 1;
        //счётчик для прохода по строкам листа
        $row = 2;

        //Записывает заголовки в первой строке листа
        $sheet->setCellValue('A1', 'Наименование');
        $sheet->setCellValue('B1', 'Бренд');
        $sheet->setCellValue('C1', 'Стоимость');
        $sheet->setCellValue('D1', 'Изображение');
        $sheet->setCellValue('E1', 'Описание');

        //Устанавливает ширину столбца  E (описание)
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(85);
        //Высота ряда
        $rowHeight = 85;
        //Получает ширину столбца  D (изображение)
        $colWidth = $spreadsheet->getActiveSheet()->getColumnDimension('D')->getWidth();

        //Устанавливает перенос по словам в столбце E (описание)
        $spreadsheet->getActiveSheet()->getStyle('E:E')
            ->getAlignment()->setWrapText(true);
        //Устанавливает выравнивание ПО ЦЕНТРУ ПО ГОРИЗОТНАЛИ в столбцах A:D
        $spreadsheet->getActiveSheet()->getStyle('A:D')
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        //Устанавливает выравнивание ПО ЦЕНТРУ ПО ВЕРТИКАЛИ в столбцах A:E
        $spreadsheet->getActiveSheet()->getStyle('A:E')
            ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        //проходит по массиву с моделями, записывая информацию из них в заданные ячейки
        while($i < $count) {
            foreach($collection as $id => $model) {

                //Устанавивает высоту ряда
                $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight($rowHeight);
                //записывает наименование продукта в Столбец  A
                $sheet->setCellValue('A' . $row, $model['product_name']);

                //записывает брэнд продукта в Столбец  B
                $sheet->setCellValue('B' . $row, $model['brand']['brand_name']);

#До сих пор не понял, как эта связка работает
# Как мы попадаем на другую таблицу - я хз

                //записывает актуальную цену в Столбец  C
                if($model['price_discounted'] != 0) {
                    $sheet->setCellValue('C' . $row, $model['price_discounted']);
                } else {
                    $sheet->setCellValue('C' . $row, $model['price_base']);
                }

                //записывает изображение в Столбец  D
                //создаёт объект изображения
                $drawing = new Drawing();
                $drawing->setName($model['product_name']);
                $drawing->setDescription($model['product_name']);
                //Указывает путь к изображению. !!!ВНИМАНИЕ!!! обратные слеши для  Windows
                $drawing->setPath('uploads\shop\pic' . '\\' . $model['brand']['brand_name']  . '\\' . $model['image_path']);
                //Устанавливает ячейку для записи изобоажения
                $drawing->setCoordinates('D' . $row);
                //Устанавливает высоту изображения
                $drawing->setHeight($rowHeight*0.75);
                //Центрует изображение по горизонтали
                $this->centerDrawingHorizontal($drawing, $colWidth);
                //Центрует изображение по вертикали
                $this->centerDrawingVertical($drawing, $rowHeight);
                //PhpSpreadsheet creates the link between the drawing and the worksheet
                //Без этой строки изображение не пишется
                $drawing->setWorksheet($spreadsheet->getActiveSheet());

                // записывает описание продукта Столбец  E
                $sheet->setCellValue('E' . $row, $model['product_description']);
                //сначала увеличиваем значение ряда, чтобы не затирать, то что записали
                $row++;
                //затем инкрементируем счётчик, чтобы прокти проверку  while()
                $i++;
            }
        }
        //записывает информацию из книги в файл !!!УТОЧНИТЬ!!!
        $writer = new Xlsx($spreadsheet);
        //Сохраняет файл книги с заданным названием
        $writer->save('XLS-shop-'. date('d-m-y Hi') . '.xls');

    }

    /**
     * записывает выбранную пользователем подборку с первой страницы в .CSV
     */
    public function writeToFile()
    {
        $handle = fopen('shop-'. date('d-m-y Hi') . '.csv', 'w');
        $collection = $this->search()->getModels();
        $count = $this->search()->getCount();
        $i = 1;
        fwrite($handle, 'Наименование; Бренд; Стоимость; Описание;' . PHP_EOL);
        while($i < $count) {
            foreach($collection as $id => $model) {

                $string = $model['product_name']. ';';
                #До сих пор не понял, как эта связка работает
                # Как мы попадаем на другую таблицу - я хз
                $string .=  $model['brand']['brand_name']. ';';
                if($model['price_discounted'] != 0) {
                    $string .= $model['price_discounted'] . ';';
                } else {
                    $string .= $model['price_base']. ';';
                }
                $string .=  $model['product_description']. ';';
                $string .=   PHP_EOL;

                fwrite($handle, $string);

                $i++;
            }
        }
        fclose($handle);
    }
}