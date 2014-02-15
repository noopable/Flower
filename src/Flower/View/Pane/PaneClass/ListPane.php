<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\PaneRenderer;

/**
 * Description of ListPane
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ListPane extends Pane implements CallbackRenderInterface
{
    use ListContainerCallbackRenderTrait;

    protected static $factoryClass = 'Flower\View\Pane\Factory\ListPaneFactory';

    public function __construct()
    {
        parent::__construct();
        $this->containerTag = 'ul';
        $this->wrapTag = 'li';
        $this->tag = 'span';
    }

}
