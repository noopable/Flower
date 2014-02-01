<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Navigation;

use Flower\View\Pane\PaneRenderer;
use RecursiveIteratorIterator;
/**
 * Description of NavigationRenderer
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class NavigationRenderer extends PaneRenderer
{

    public function __construct(PaneInterface $pane,
            $mode = RecursiveIteratorIterator::SELF_FIRST,
            $flag = RecursiveIteratorIterator::CATCH_GET_CHILD)
    {
        parent::__construct($pane, $mode, $flag);
    }
}
