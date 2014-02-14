<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\Exception\RuntimeException;
use RecursiveIterator;

/**
 * Description of Collection
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Collection implements PaneInterface, CollectionAwareInterface
{
    use PaneTrait, CollectionAwareTrait;

    protected $prototype;

    public function setPrototype(EntityAwareInterface $prototype)
    {
        $this->prototype = $prototype;
    }

    public function getPrototype()
    {
        if (! is_object($this->prototype)) {
            throw new RuntimeException('Collection needs prototype');
        }
        return clone $this->prototype;
    }

    public function current()
    {
        $entity = $this->getCollection()->current();
        $prototype = $this->getPrototype();
        $prototype->setEntity($entity);
        return $prototype;
    }

    public function getChildren()
    {
        $collection = $this->getCollection();
        if (! $collection instanceof RecursiveIterator) {
            return;
        }
        return $collection->getChildren();
    }

    public function hasChildren()
    {
        $collection = $this->getCollection();
        if (! $collection instanceof RecursiveIterator) {
            return false;
        }
        return $collection->hasChildren();
    }

    public function insert($value, $priority = null)
    {
        throw new RuntimeException('Collection create pane dynamic. Thus cant insert pane now. You can extend this to append or prepend pane ');
    }

    public function key()
    {
        return $this->getCollection()->key();
    }

    public function next()
    {
        return $this->getCollection()->next();
    }

    public function rewind()
    {
        return $this->getCollection()->rewind();
    }

    public function valid()
    {
        return $this->getCollection()->valid();
    }

}