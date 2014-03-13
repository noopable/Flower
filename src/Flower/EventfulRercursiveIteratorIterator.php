<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
/**
 * 
 * 
 * 
 * まだ使えません。
 * 
 * 
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
namespace Flower;

use RecursiveIteratorIterator;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventInterface;
use Zend\EventManager\Event;

class EventfulRecursiveIteratorIterator extends RecursiveIteratorIterator
{
    use EventManagerAwareTrait;
    
    /**
     * event object 
     * 
     * @var Zend\EventManager\Event
     */
    public $event;
    
    public $enableMethods = array();
    
    public function setEvent(EventInterface $e)
    {
        $this->event = $e;
    }
    
    public function getEvent()
    {
        if (!isset($this->event) || ! $this-event instanceof EventInterface) {
            $this->event = new Event;
        }
        return $this->event;
    }
    
    public function __construct(Traversable $iterator
                              , $mode = RecursiveIteratorIterator::LEAVES_ONLY
                              , $flags = 0
                              , $enableMethods = null)
     {
         parent::__construct($iterator, $mode, $flags);
         if (is_string($enableMethods)) {
             $enableMethods = array($enableMethods);
         }
         if (is_array($enableMethods)) {
             $methods = array();
              foreach($this->enableMethods as $method) {
                  if (is_string($method) && method_exists($this, $method)) {
                     $methods[] = $method;
                 }
             }
             $this->enableMethods = $methods;
         }
     }
     
    /**
     * Register the default events for this controller
     *
     * @return void
     */
    protected function attachEnabledListeners()
    {
        $events = $this->getEventManager();
        foreach($this->enableMethods as $method) {
             $events->attach($method, array($this, $method));
        }
    }
}