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
class Collection implements PaneInterface, CollectionAwareInterface, EntityPrototypeAwareInterface
{
    use PaneTrait, CollectionAwareTrait;

    protected static $factoryClass = 'Flower\View\Pane\Factory\CollectionFactory';

    protected $prototype;

    protected $children = array();

    public $_var;

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
        $key = $this->key();
        if (!isset($this->children[$key])) {
            $this->children[$key] = $this->getPrototype();
        }

        $entity = $this->getCollection()->current();
        $this->children[$key]->setEntity($entity);
        return $this->children[$key];
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
