<?php

namespace app\modules\admin\modules\product\models;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Export
{
    //Количество элементов на странице для экспорта (ставим 1000, чтобы не нагружать БД)
    private $pageSize = 1;

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
            $drawing->setWidth($colWidthPixels * 0.9);
        }
        //pixels
        $offsetX = ($colWidthPixels - $drawing->getWidth()) / 2;
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
     * вохвращает выбранную пользователем подборку с первой страницы в объект SpreadSheet для записи в .XLS
     *
     * @param object $dataProvider yii\data\ActiveDataProvider  полученный после выполнения поиска на странице ПРОДУКТ
     * @return object Spreadsheet PhpOffice\PhpSpreadsheet\Spreadsheet;
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function export($dataProvider)
    {
        //Создаёт объект книги
        $spreadsheet = new Spreadsheet();
        //Устанавливает акивный лист
        $sheet = $spreadsheet->getActiveSheet();

        //the total number of data models.
        $totalModelCount = $dataProvider->getTotalCount();
        // Устанавливаем размер страницы (количество моделей на странице)
        $dataProvider->getPagination()->setPageSize($this->pageSize);

        //Вычисляем общее количество страниц
        $totalPages = ceil($totalModelCount / $this->pageSize);
        // Записываем в переменную номер текущей страницы
        $currentPage = 0;
        //счётчик для прохода по строкам листа
        $row = 2;

        //Записывает заголовки в первой строке листа
        $sheet->setCellValue('A1', 'Наименование');
        $sheet->setCellValue('B1', 'Бренд');
        $sheet->setCellValue('C1', 'Стоимость');
        $sheet->setCellValue('D1', 'Изображение');
        $sheet->setCellValue('E1', 'Описание');

        //Устанавливает ширину столбца  E (описание)
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(95);
        //Высота ряда
        $rowHeight = 95;
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


        while ($currentPage < $totalPages) {
            //Ставим номер страницы объекта пагинации (начиная с 0)
            $dataProvider->getPagination()->setPage($currentPage);
            // Обновляет данные датапровайдера.
            $dataProvider->refresh();
            //The list of data models in the current page.
            $collection = $dataProvider->getModels();

            foreach ($collection as $id => $model) {
                //Устанавивает высоту ряда
                $spreadsheet->getActiveSheet()->getRowDimension($row)->setRowHeight($rowHeight);

                //записывает наименование продукта в Столбец  A
                $sheet->setCellValue('A' . $row, $model['product_name']);

                //записывает брэнд продукта в Столбец  B
                $sheet->setCellValue('B' . $row, $model['brand']['brand_name']);

                //записывает актуальную цену в Столбец  C
                if ($model['price_discounted'] != 0) {
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
                $drawing->setPath('uploads\shop\pic' . '\\' . $model['brand']['brand_name'] . '\\' . $model['image_path']);
                //Устанавливает ячейку для записи изобоажения
                $drawing->setCoordinates('D' . $row);
                //Устанавливает высоту изображения
                $drawing->setHeight($rowHeight * 0.75);
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
            }
            //инкремент номера страницы
            $currentPage++;
        }

        // для передачи пользователю
        return $spreadsheet;
    }

    /**
     * записывает выбранную пользователем подборку в строку с разделителем ";", для .CSV
     *
     * @param object $dataProvider yii\data\ActiveDataProvider  полученный после выполнения поиска на странице ПРОДУКТ
     * @return string подборка после фильтров с разделителем ";", для .CSV
     */
    public function writeToFile($dataProvider)
    {
        //the total number of data models.
        $totalModelCount = $dataProvider->getTotalCount();
        // Устанавливаем размер страницы (количество моделей на странице)
        $dataProvider->getPagination()->setPageSize($this->pageSize);

        //Вычисляем общее количество страниц
        $totalPages = ceil($totalModelCount / $this->pageSize);
        // Записываем в переменную номер текущей страницы
        $currentPage = 0;
        //Общий счётчик моделей
        $indexTotalModel = 1;

        //добавляет UTF-8 BOM для чтения кириллицы в Excel
        $BOM = "\xEF\xBB\xBF";
        //Записывает заголовок
        $topLine = '№; Наименование; Бренд; Стоимость; Описание;' . PHP_EOL;

        $string = '';
        $string .= $BOM . $topLine;

        while ($currentPage < $totalPages) {
            //Ставим номер страницы объекта пагинации (начиная с 0)
            $dataProvider->getPagination()->setPage($currentPage);

            // Обновляет данные датапровайдера.
            $dataProvider->refresh();

            //The list of data models in the current page.
            $collection = $dataProvider->getModels();

            foreach ($collection as $id => $model) {
                //номер по порядку
                $string .= $indexTotalModel . ';';
                //название
                $string .= $model['product_name'] . ';';
                //бренд
                $string .= $model['brand']['brand_name'] . ';';
                //выбор финальной цены (скидочной, если она есть, в противном случае - базовой)
                if ($model['price_discounted'] != 0) {
                    $string .= $model['price_discounted'] . ';';
                } else {
                    $string .= $model['price_base'] . ';';
                }
                //описание
                $string .= $model['product_description'] . ';';
                //конец строци
                $string .= PHP_EOL;

                //инкремент общего счётчика моделей
                $indexTotalModel++;
            }
            //инкремент номера страницы
            $currentPage++;
        }

        return $string;
    }
}