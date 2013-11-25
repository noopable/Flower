<?php
namespace Flower;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use DateTime;

class TimeUtils
{
    public static function mysqlFormatDatetime($time = null)
    {
        if (is_numeric($time)) {
            $datetime = new DateTime;
            $datetime->setTimestamp($time);
        }
        else {
            $datetime = new DateTime($time);
        }

        return $datetime->format("Y-m-d H:i:s");
    }

    public static function mysqlFormatDatetimeString()
    {
        return "Y-m-d H:i:s";
    }
}