<?php
/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer\Events;

use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Description of EventsProxy
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class EventsProxy implements EventManagerAwareInterface {

    protected $events;
    
    const EVENT_INVOKE = 'service_layer_invoke';
    
    protected $innerObject;
    
    public function __construct($instance = null)
    {
        $this->innerObject = $instance;
    }
   
    public function passThrough()
    {
        return $this->innerObject;
    }
    
    public function proxy(Event $e)
    {
        $target = $e->getTarget();
        $params = $e->getParams();
        $method = $params['method'];
        $args = $params['args'];
        return call_user_func_array(array($target, $method), $args);
    }
    
    /**
     * Set the event manager instance used by this context
     *
     * @param  EventManagerInterface $events
     * @return AbstractController
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $identifiers = array(
            get_class($this),
            'Flower\ServiceLayer\ServiceLayerInterface',
        );
        if (isset($this->innerObject) && is_object($this->innerObject)) {
            $class = get_class($this->innerObject);
            //Serviceクラス
            $identifiers[] = $class;
            //Serviceクラスのnamespace
            $identifiers[] = substr($class, 0, strrpos($class, '\\'));
        }
        $events->setIdentifiers($identifiers);
        $events->attach(self::EVENT_INVOKE, array($this, 'proxy'));
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
    
    /**
     * 
     * @param type $name
     * @param type $arguments
     * @return type
     * @throws RuntimeException
     */
    public function __call($name, $arguments)
    {
        $params = array(
            'method' => $name,
            'args' => $arguments,
        );
        $e = new Event(self::EVENT_INVOKE, $this->innerObject, $params);
        $response =  $this->getEventManager()->trigger(self::EVENT_INVOKE, $e);
        return $response->last();
    }
}
