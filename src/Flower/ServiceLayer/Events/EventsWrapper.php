<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer\Events;

use Flower\ServiceLayer\Wrapper\AbstractWrapper;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
/**
 * Description of EventsWrapper
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class EventsWrapper extends AbstractWrapper implements EventManagerAwareInterface {
    
    protected $events;
    
    /**
     * 
     * @return \Flower\ServiceLayer\Wrapper\ProxyFactoryInterface 
     */
    public function getProxyFactory()
    {
        if (!isset($this->proxyFactory)) {
            $this->proxyFactory = new EventsProxyFactory($this->getEventManager());
        }
        return $this->proxyFactory;
    }
    
    /**
     * Set the event manager instance used by this context
     *
     * @param  EventManagerInterface $events
     * @return AbstractController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;
        return $this;
    }
    
    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (!$this->events instanceof EventManagerInterface) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }
}
