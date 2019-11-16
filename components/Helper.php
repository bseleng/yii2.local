<?php

namespace app\components;

/**
 * Class Helper
 * @package app\components
 */
/**
 * Class Helper
 * @package app\components
 */
class Helper
{
    /**
     * возвращает значение GET параметра 'page'
     * @param int $page номер страницы
     * @return int значение GET параметра 'page'
     */
    public static function getPageNumber($page)
    {
        if (!isset($page)) {
            $page = 1;
        }

        return $page;
    }

    /**
     * рассчитывает смещение согласно текузей страницы и заданного лимита
     * @param int $limit лимит, который требуется вывести, по умолчанию = 3
     * @param int $page номер страницы
     * @return int значение смещения
     */
    public static function getOffset($limit, $page)
    {
        //$offset = $limit * (self::getPageNumber($page) - 1);
        $offset = $limit * ($page - 1);

        return $offset;
    }

    /**
     * удаляет заданное количество символов с начали и сконца строки
     * @param $str строрка, которую нужно отредактировать
     * @param $start сколько символов удалить с начала
     * @param $end сколько символов удалить с конца
     * @return bool|string строка без заданного количества символов с начала и с конца
     */
    public static  function trimStartEnd($str, $start, $end)
    {
        $truLength = strlen($str) - $start - $end;
        $str = substr($str,$start,$truLength);

        return $str;
    }

    /**
     * проверяет верхнию и нижнюю границы значения и приводит к одной из них в случае несоответствия
     * @param int $value значение для проверки
     * @param int $max верхняя допустимая граница
     * @param int $min нижняя допустимая граница
     * @return int значение в установленных рамках
     */
    public static function checkValue($value, $max, $min)
    {
        if ($value < $min) {
            $value = $min;
        } elseif ($value > $max) {
            $value = $max;
        }
        return $value;
    }


    public static function incrementValue($initialValue = 1)
    {
        if(isset($initialValue)) {
            $initialValue++;
        }
        return $initialValue;
    }

}