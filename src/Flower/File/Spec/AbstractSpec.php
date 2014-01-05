<?php
namespace Flower\File\Spec;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Traversable;

use Zend\Stdlib\ArrayUtils;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventsCapableInterface;
use Zend\EventManager\ListenerAggregateInterface;

use Flower\File\Event;
use Flower\File\Spec\SpecSetterGetterTrait;
/**
 * ファイル管理を構成する基本的な仕様
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
abstract class AbstractSpec implements SpecInterface, ListenerAggregateInterface 
{
    use SpecSetterGetterTrait;
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    
    public function __construct($config = array())
    {
        if (is_object($config) && $config instanceof Traversable) {
            $config = ArrayUtils::iteratorToArray($config);
        }
        $this->config = $config;
    }
    
    public function configure()
    {
        /**
        * Gateway Eventにリスナーを登録
        */
        if ($this->gateway instanceof EventsCapableInterface) {
            $events = $this->gateway->getEventManager();
            $Listeners = array(
                $this, 
                $this->cacheSpec, 
                $this->resolveSpec,
                $this->mergeSpec,
                $this->fileAdapter,
            );
            
            foreach($Listeners as $object) {
                if (is_object($object) && $object instanceof ListenerAggregateInterface) {
                    $events->attachAggregate($object);
                }
            }
        }
    }
    
    public function attach(EventManagerInterface $events)
    {
        $this->lisetners[] = $events->attach(Event::EVENT_READ, [$this, 'onRead']);
        $this->lisetners[] = $events->attach(Event::EVENT_WRITE, [$this, 'onWrite']);
        $this->lisetners[] = $events->attach(Event::EVENT_MERGE, [$this, 'onMerge']);
    }
    
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }
    
    /**
     * Default Spec to read
     * 
     * @param \Flower\File\Event $e
     */
    public function onRead(Event $event)
    {
        /**
         * set resolved files to the namedFiles with specified name 
         */
        $this->gateway->resolve($event, Event::RESOLVE_READ);

        $namedFiles = $event->getNamedFiles();
        
        if ($namedFiles->count() === 0) {
            //resolve failed 
            error_log($namedFiles->getName() .': resolve failed', E_USER_WARNING);
            return null;
        }
        
        $events = $this->gateway->getEventManager();
        // Trigger Read events
        $events->trigger(Event::EVENT_FILE_READ, $event);
        
        if ($namedFiles->count() === 1 && !$event->getData()) {
            $data = $namedFiles->getFile()->getValue();
        }
        else {
            $result = $events->trigger(Event::EVENT_MERGE, $event);
            $data = $result->last();
            $data = $event->getData();
        }
        
        $event->setData($data);
        
        /**
         * don't return data as a Response object.
         * 
         */
        return $data;
    }
    
    public function onWrite(Event $event)
    {
        $event->setResolveMode(Event::RESOLVE_WRITE);
        /**
         * set resolved files to the namedFiles with specified name 
         */
        $this->gateway->resolve($event, Event::RESOLVE_WRITE);

        $namedFiles = $event->getNamedFiles();
        
        if ($namedFiles->count() === 0) {
            //resolve failed 
            error_log($namedFiles->getName() .': not found', E_USER_NOTICE);
            $this->gateway->resolve($event, Event::RESOLVE_CREATE);
            if ($namedFiles->count() === 0) {
                throw new \RuntimeException($namedFiles->getName() .' resolve faild for creation');
            }
        }
        
        //基本的に上書き用の最後のファイルへ書き込む
        $fileInfo = $namedFiles->getFile($event->getParam('extension', null), false);
        $fileInfo->setValue($event->getData());
        $namedFiles->clearFiles();
        $namedFiles->setFile($fileInfo);
        
        $events = $this->gateway->getEventManager();
        // Trigger Read events
        $res = $events->trigger(Event::EVENT_FILE_WRITE, $event);
        
        $events->trigger(Event::EVENT_REFRESH, $event);
        
        //create cache
        $this->gateway->read($event->getDataName());
        /**
         * don't return data as a Response object.
         * 
         */
        return $res->last();
        
    }
    
    public function onMerge(Event $e)
    {
        $value = $e->getData();
        $files = $e->getNamedFiles();
        
        foreach ($files as $fileInfo) {
            if (!isset($value)) {
                $value = $fileInfo->getValue();
                continue;
            }
            if (isset($this->mergeSpec)) {
                $value = $this->mergeSpec->merge($value, $fileInfo->getValue());
            }
            else {
                $value = $fileInfo->getValue();
            }
            
        }
        $e->setData($value);
        return $value;
    }
}
