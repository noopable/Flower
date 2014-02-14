<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Iterator;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface CollectionAwareInterface
{

    public function setCollection(Iterator $collection);

    public function getCollection();
}
