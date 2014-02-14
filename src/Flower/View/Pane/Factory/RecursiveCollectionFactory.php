<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Factory;

/**
 * CollectionPrototypeを追加すべきでしょうか。
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RecursiveCollectionFactory extends CollectionFactory
{
    protected static $paneClass = 'Flower\View\Pane\PaneClass\RecursiveCollection';
}
