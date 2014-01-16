<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer\Events;

use Flower\ServiceLayer\ServiceLayerInterface;
use Flower\ServiceLayer\Wrapper\ProxyFactoryInterface;
use Zend\EventManager\EventManager;
/**
 * Description of EventsProxyFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class EventsProxyFactory implements ProxyFactoryInterface {

    protected $events;
    
    public function __construct(EventManager $events)
    {
        $this->events = $events;
    }
    
    public function factory(ServiceLayerInterface $service)
    {
        $wrapped = new EventsProxy($service);
        $wrapped->setEventManager($this->events);
        return $wrapped;
    }

}
