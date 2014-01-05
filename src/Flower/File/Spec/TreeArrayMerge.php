<?php
namespace Flower\File\Spec;
/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\EventManager\EventManagerInterface;
use Flower\File\Event;

/**
 * Description of Config
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class TreeArrayMerge extends AbstractSpec {

    public function attach(EventManagerInterface $events)
    {
        parent::attach($events);
        $this->listeners[] = $events->attach(Event::EVENT_READ, [$this, 'onRead_pre'], 10);
        //@see AbstractSpec
        //$this->lisetners[] = $events->attach(Event::EVENT_READ, [$this, 'onRead']);
        $this->listeners[] = $events->attach(Event::EVENT_READ, [$this, 'onRead_post'], -10);
    }

    /**
     * 通常のgatewayに先行してキャッシュスペックによるキャッシュ探索を行う。
     *
     * @param \Flower\File\Event $e
     * @return type
     */
    public function onRead_pre(Event $event)
    {
        $gateway = $this->getGateway();
        $events = $gateway->getEventManager();

        $events->trigger(Event::EVENT_CACHE_READ, $event);

        if ($data = $event->getData()) {
            $event->stopPropagation(true);
            return $data;
        }

        /**
         * ディレクトリ構造をたどるTreeArrayMerge固有の動作
         */
        $name = $event->getDataName();
        if ($pos = strrpos($name, '/')) {
            $parent = substr($name, 0, $pos);
            $data = $gateway->read($parent);
            $event->setData($data);
            //値は返さない
        }
    }

    public function onRead_post(Event $event)
    {
        $gateway = $this->getGateway();
        $events = $gateway->getEventManager();
        $events->trigger(Event::EVENT_CACHE_MAKE, $event);
        return $event->getData();
    }
}
