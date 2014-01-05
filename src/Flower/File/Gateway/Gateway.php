<?php
namespace Flower\File\Gateway;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\EventManager\EventsCapableInterface;
use Zend\EventManager\ProvidesEvents;

use Flower\File\Event;
/**
 *
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Gateway implements GatewayInterface, EventsCapableInterface {
    use ProvidesEvents;

    protected $lastEvent;

    /**
     *
     * @var Event
     */
    protected $eventPrototype;

    public function resolveAll($nameOrEvent)
    {
        if (is_string($nameOrEvent)) {
            $event = $this->getEvent($nameOrEvent);
        }
        elseif ($nameOrEvent instanceof Event) {
            $event = $nameOrEvent;
        }
        $event->setResolveMode(Event::RESOLVE_ALL);
        return $this->getEventManager()->trigger(Event::EVENT_RESOLVE, $event);
    }

    /**
     *
     * @param Event|string $nameOrEvent
     * @return String
     */
    public function resolve($nameOrEvent, $resolveMode = null)
    {
        if (is_string($nameOrEvent)) {
            $event = $this->getEvent($nameOrEvent);
            if (null === $resolveMode) {
                $event->setResolveMode(Event::RESOLVE_READ);
            }
        }
        elseif ($nameOrEvent instanceof Event) {
            $event = $nameOrEvent;
        }

        if (null !== $resolveMode) {
            $event->setResolveMode($resolveMode);
        }

        $shortCircuit = function ($r)  {
            if ($r instanceof NamedFiles) {
                if ($r->count() > 0) {
                    return true;
                }
            }
            return false;
        };

        $events = $this->getEventManager();
        $res = $events->trigger(Event::EVENT_RESOLVE, $event, $shortCircuit);
        //$lastResponse = $res->last();
        return $res->last();
    }

    /**
     * 名前を指定してマージ済みのデータが取得したい。
     *
     * @param type $name
     * @return type
     */
    public function read($name)
    {
        $event = $this->getEvent($name);
        if ($event->getData()) {
            return $event->getData();
        }
        $events = $this->getEventManager();
        $res = $events->trigger(Event::EVENT_READ, $event);
        return $res->last();
    }

    /**
     * 指定された名前と拡張子のファイルに保存する。
     * extentionがnullの場合は、最初に解決されたファイルに保存する。
     *
     * @param type $name
     * @param type $data
     * @param type $extention
     */
    public function write($name, $data, $extension = null)
    {
        $event = $this->getEvent($name);
        $event->setData($data);
        $event->setParam('extension', $extension);
        $events = $this->getEventManager();
        $shortcut = function ($r) {
            if ($r) {
                return true;
            }
            return false;
        };
        $res = $events->trigger(Event::EVENT_WRITE, $event, $shortcut);
        return $res->last();
    }

    public function namedFilesWrite(NamedFiles $namedFiles)
    {
        //各種ファイルにそれぞれ書き込む
    }


    public function getLastEvent()
    {
        return $this->lastEvent;
    }

    public function setLastEvent(Event $lastEvent)
    {
        $this->lastEvent = $lastEvent;
    }

    public function getEvent($name, $eventName = null)
    {
        if (null === $name) {
            $name = '';
        }

        if (! isset($this->eventCollection[$name])) {
            $event = new Event($eventName);
            $event->setTarget($this);
            $event->setDataName($name);
            $this->eventCollection[$name] = $event;
        }
        $this->setLastEvent($this->eventCollection[$name]);
        return $this->eventCollection[$name];
    }

    public function refresh($name = null)
    {
        $event  = $this->getEvent($name);
        $event->setData(null);
        return $this->getEventManager()->trigger(Event::EVENT_REFRESH, $event);
    }
}
