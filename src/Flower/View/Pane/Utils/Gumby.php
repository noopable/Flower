<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Utils;

/**
 * Description of GumbySizeToClass
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Gumby
{
    public static $nums = array( "zero", "one", "two", "three", "four", "five", "six", "seven",
                    "eight", "nine", "ten", "eleven", "twelve", "thirteen",
                    "fourteen", "fifteen", "sixteen", "seventeen", "eighteen",
                    "nineteen", "twenty",);

    public static function sizeToClass($size)
    {
        $size = (int) $size;
        if (!isset(static::$nums[$size])) {
            return '';
        }
        return static::$nums[$size] . ' columns';
    }

}
