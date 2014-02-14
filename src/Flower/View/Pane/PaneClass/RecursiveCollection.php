<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use RecursiveIterator;

/**
 * @notice getCollection内、clone でよいのか慎重に検討してください。
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RecursiveCollection extends Collection
{
    public function getChildren()
    {
        $collection = $this->getCollection();
        if (! $collection instanceof RecursiveIterator) {
            return;
        }
        $prototype = clone $this;
        $prototype->resetEntriesCache();
        $prototype->setCollection($collection->getChildren());
        return $prototype;
    }

    public function hasChildren()
    {
        $collection = $this->getCollection();
        if (! $collection instanceof RecursiveIterator) {
            return false;
        }
        return $collection->hasChildren();
    }

}
