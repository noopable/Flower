<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\Exception\RuntimeException;
use Iterator;
use IteratorAggregate;

/**
 * Description of AnchorCollection
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class AnchorCollection extends Anchor implements CollectionAwareInterface, EntityPrototypeAwareInterface
{
    use CollectionAwareTrait, ListContainerCallbackRenderTrait;

    protected static $factoryClass = 'Flower\View\Pane\Factory\AnchorCollectionFactory';

    protected $prototype;

    protected $iterator;

    protected $children = array();

    public function setPrototype(EntityAwareInterface $prototype)
    {
        $this->prototype = $prototype;
    }

    public function getPrototype()
    {
        if (!isset($this->prototype)) {
            $this->prototype = new EntityScriptPane;
        }

        if (! is_object($this->prototype)) {
            throw new RuntimeException('Collection needs prototype');
        }

        return clone $this->prototype;
    }

    public function current()
    {
        $collection = $this->getIterator();
        if (null === $collection) {
            return;
        }

        $key = (string) $this->key();

        if ($key) {
            if (!isset($this->children[$key])) {
                $this->children[$key] = $this->getPrototype();
            }

            $entity = $this->getIterator()->current();
            $this->children[$key]->setEntity($entity);
            return $this->children[$key];
        } else {
            $prototype = $this->getPrototype();
            $entity = $this->getIterator()->current();
            $prototype->setEntity($entity);
            return $prototype;
        }
    }

    public function getChildren()
    {
    }

    /**
     * This is not recursive. Iterates collection only
     * @return false
     */
    public function hasChildren()
    {
        return false;
    }

    public function resetEntriesCache()
    {
        $this->children = array();
    }

    public function insert($value, $priority = null)
    {
        throw new RuntimeException('Collection create pane dynamic. Thus cant insert pane now. You can extend this to append or prepend pane ');
    }

    public function key()
    {
        $collection = $this->getIterator();
        if (null === $collection) {
            return;
        }
        return $collection->key();
    }

    public function next()
    {
        return $this->getIterator()->next();
    }

    public function rewind()
    {
        $collection = $this->getIterator();
        if (null === $collection) {
            return;
        }
        return $this->getIterator()->rewind();
    }

    public function valid()
    {
        $collection = $this->getIterator();
        if (null === $collection) {
            return false;
        }
        return $collection->valid();
    }

    public function getIterator()
    {
        $collection = $this->getCollection();
        if ($collection instanceof Iterator) {
            $this->iterator = $collection;
        } elseif ($collection instanceof IteratorAggregate) {
            $this->iterator = $collection->getIterator();
        }
        return $this->iterator;
    }
}
