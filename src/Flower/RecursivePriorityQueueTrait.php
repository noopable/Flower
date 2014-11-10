<?php
/*
 *
 *
 * @copyright Copyright (c) 2014-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower;

use Zend\Stdlib\PriorityQueue;

/**
 * RecursivePriorityQueue
 *   implements RecursiveIterator
 *
 * 優先度付きのキューを再帰的にIterationするためのTrait
 * rewind可
 *
 * @author Tomoaki Kosugi <kipspro@gmail.com>
 */
trait RecursivePriorityQueueTrait
{
    /**
     * Using RecursivePriorityQueueTrait or not
     *
     * @var boolean
     */
    public $recursivePriorityQueueTrait = true;

    /**
     * Inner queue class to use for iteration
     * @var string
     */
    protected $queueClass = 'Zend\Stdlib\PriorityQueue';

    /**
     * Inner queue object
     * @var SplPriorityQueue
     */
    protected $queue;

    /**
     *
     * @var \Iterator
     */
    protected $innerIterator;

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
        $this->rewind();
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
                throw new DomainException(sprintf(
                    'PriorityQueue expects an internal queue of type PriorityQueue; received "%s"', get_class($this->queue)
                ));
            }
        }
        return $this->queue;
    }

    public function isEmpty()
    {
        return $this->getQueue()->isEmpty();
    }

    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    public function getChildren()
    {
        if ($this->hasChildren()) {
            //The current is also recursiveIterater having item(s) in its queue.
            return $this->current();
        }
    }

    public function hasChildren()
    {
        return (($current = $this->current())
                && is_object($current)
                && isset($current->recursivePriorityQueueTrait)
                && $current->recursivePriorityQueueTrait
                && ! $current->isEmpty());
    }

    public function key()
    {
        return $this->getInnerIterator()->key();
    }

    public function next()
    {
        return $this->getInnerIterator()->next();
    }

    public function rewind()
    {
        $this->resetIterator();
    }

    public function getInnerIterator()
    {
        if (!isset($this->innerIterator)) {
            $this->resetIterator();
        }
        return $this->innerIterator;
    }

    public function resetIterator()
    {
        $this->innerIterator = $this->getQueue()->getIterator();
    }

    public function valid()
    {
        return $this->getInnerIterator()->valid();
    }
}
