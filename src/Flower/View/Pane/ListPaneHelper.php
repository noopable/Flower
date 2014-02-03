<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

/**
 * Description of ListPaneHelper
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ListPaneHelper extends PaneHelper
{
    public $defaultPaneClass = 'Flower\View\Pane\ListPane';

    public $paneRenderer = 'Flower\View\Pane\ListRenderer';
}
