<?php
namespace Flower;

/*
 * 
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Stdlib\PriorityQueue;
/**
 * Description of RecursivePriorityQueue
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RecursivePriorityQueue implements \RecursiveIterator
{

    const EXTR_DATA     = 0x00000001;
    const EXTR_PRIORITY = 0x00000002;
    const EXTR_BOTH     = 0x00000003;
    
    const HAS_CHILDREN_STRICT_CONTAINS = 0x00010000;

    /**
     * Inner queue class to use for iteration
     * @var string
     */
    protected $queueClass = 'Zend\Stdlib\PriorityQueue';

    /**
     * Actual items aggregated in the priority queue. Each item is an array
     * with keys "data" and "priority".
     * @var array
     */
    protected $items = array();

    /**
     * Inner queue object
     * @var SplPriorityQueue
     */
    protected $queue;

    /**
     *
     * @var \Iterator
     */
    protected $iterator;
    
    /**
     *
     * @var type 
     */
    protected $flag;

    public function __construct($flag = 0)
    {
        $this->flag = $flag;
        $this->rewind();
    }

    /**
     * Insert an item into the queue
     *
     * Priority defaults to 1 (low priority) if none provided.
     *
     * @param  mixed $data
     * @param  int $priority
     * @return PriorityQueue
     */
    public function insert($data, $priority = null)
    {
        if (null === $priority) {
            $priority = 1;
        }
        $this->getQueue()->insert($data, $priority);
        $this->iterator->insert($data, $priority);
        return $this;
    }

    /**
     * Remove an item from the queue
     *
     * This is different than {@link extract()}; its purpose is to dequeue an
     * item.
     *
     * This operation is potentially expensive, as it requires
     * re-initialization and re-population of the inner queue.
     *
     * Note: this removes the first item matching the provided item found. If
     * the same item has been added multiple times, it will not remove other
     * instances.
     *
     * @param  mixed $datum
     * @return bool False if the item was not found, true otherwise.
     */
    public function remove($datum)
    {
        $res = $this->getQueue()->remove($datum);
        $this->rewind();
        return $res;
    }

    /**
     * Get the inner priority queue instance
     *
     * @throws \DomainException
     * @return SplPriorityQueue
     */
    protected function getQueue()
    {
        if (null === $this->queue) {
            $this->queue = new $this->queueClass();
            if (!$this->queue instanceof PriorityQueue) {
                throw new \DomainException(sprintf(
                    'PriorityQueue expects an internal queue of type PriorityQueue; received "%s"', get_class($this->queue)
                ));
            }
        }
        return $this->queue;
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function getChildren()
    {
        if ($this->hasChildren()) {
            return $this->current();
        }
    }

    public function hasChildren()
    {
        if ($this->current() instanceof static) {
            if ($this->flag & self::HAS_CHILDREN_STRICT_CONTAINS) {
                return $this->current()->valid();
            }
            return true;
        }
        return false;
    }

    public function key()
    {
        return $this->iterator->key();
    }

    public function next()
    {
        return $this->iterator->next();
    }

    public function rewind()
    {
        $queue = $this->getQueue();
        $iterator = $this->getQueue()->getIterator();
        foreach ($iterator as $entry) {
            $this->hasChildren();
            //$entry->rewind();
        }
        $this->iterator = $this->getQueue()->getIterator();
    }

    public function valid()
    {
        return $this->iterator->valid();
    }

}
