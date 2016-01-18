<?php

/**
 *
 * @copyright Copyright (c) 2013-2016 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Utils;

/**
 * Description of Bootstrap3
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Bootstrap3
{
    /**
     * カンマ区切りでsizeを受け取り、一つならmd、2つならsmとmd、3つならxs sm md と判定する。
     *
     * @param type $size
     */
    public static function sizeToClass($size)
    {
        $classStrings = [];

        /**
        * xs < 768
        * sm > 768
        * md > 992
        * lg > 1200
        *
        * @var $pre array
        */
        $pre = [
            'xs' => null,
            'sm' => null,
            'md' => null,
            'lg' => null
        ];

        if (is_string($size)) {
            $size = explode(',', $size);
        }

        if (!is_array($size)) {
            $size = [intval($size)];
        }

        switch (count($size)) {
            case 1:
                $pre['md'] = array_shift($size);
                break;
            case 2:
                $pre['sm'] = array_shift($size);
                $pre['md'] = array_shift($size);
                break;
            case 3:
                $pre['xs'] = array_shift($size);
                $pre['sm'] = array_shift($size);
                $pre['md'] = array_shift($size);
                break;
            case 4:
                $pre['xs'] = array_shift($size);
                $pre['sm'] = array_shift($size);
                $pre['md'] = array_shift($size);
                $pre['lg'] = array_shift($size);
                break;
        }
        foreach ($pre as $k => $v) {
            if (null !== $v) {
                $classStrings[] = 'col-' . $k . '-' . intval($v) . ' ';
            }
        }

        return implode(' ', $classStrings);
    }
}
