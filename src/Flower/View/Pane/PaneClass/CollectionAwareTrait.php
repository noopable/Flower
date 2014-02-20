<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Traversable;

/**
 * Description of CollectionAwareTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait CollectionAwareTrait
{

    protected $collection;

    public function setCollection(Traversable $collection)
    {
        $this->collection = $collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }
}
