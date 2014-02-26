<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager\Listener;

use Flower\EventManager\Exception\RuntimeException;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;

/**
 * Description of CallbackSharedListenerAggregate
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class CallbackSharedListenerAggregate implements SharedListenerAggregateInterface
{

    protected $callbacks = array();

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * SharedEventManager::attach accepts $ids as array.
     * But anyway in this class, $identifier should be string
     *
     * @param string $identifier
     * @param string $event
     * @param callable $callback
     * @throws RuntimeException
     */
    public function addCallback($identifier, $event, $callback)
    {
        if (!is_callable($callback)) {
            throw new RuntimeException('specified callback is not callable:' . $identifier . '/' . $event);
        }

        if (!isset($this->callbacks[$identifier])) {
            $this->callbacks[$identifier] = array();
        }

        if (!isset($this->callbacks[$identifier][$event])) {
            $this->callbacks[$identifier][$event] = array();
        }

        $this->callbacks[$identifier][$event][] = $callback;
    }

    public function removeCallback($identifier, $event, $callback)
    {
        if (!isset($this->callbacks[$identifier])) {
            return;
        }

        if (!isset($this->callbacks[$identifier][$event])) {
            return;
        }

        $keys = array_keys($this->callbacks[$identifier][$event], $callback);
        foreach($keys as $key) {
            unset($this->callbacks[$identifier][$event][$key]);
        }
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->callbacks as $identifier =>$eventArray) {
            if (!isset($this->listeners[$identifier])) {
                $this->listeners[$identifier] = array();
            }
            foreach ($eventArray as $event => $callbacks) {
                foreach ($callbacks as $index => $callback) {
                    $this->listeners[$identifier][$index] = $events->attach($identifier, $event, $callback);
                }
            }
        }
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $identifier => $callbacks) {
            foreach ($callbacks as $index => $callback) {
                if ($events->detach($identifier, $callback)) {
                    unset($this->listeners[$identifier][$index]);
                }
            }
        }
    }

}
