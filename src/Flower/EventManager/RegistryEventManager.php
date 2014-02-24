<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager;

use Flower\EventManager\Exception\RuntimeException;
use Flower\File\Gateway\GatewayInterface;
use Traversable;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\ResponseCollection;
use Zend\Stdlib\CallbackHandler;

/**
 * Description of RegistryEventManager
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RegistryEventManager implements RegistryEventManagerInterface
{
    use EventManagerAwareTrait;

    protected $eventPluginManager;

    protected $registry;

    /**
     *
     * @var \Zend\EventManager\SharedEventManager
     */
    protected $sharedEventManager;

    /**
     * もし同一エンティティに対してイベント種を変更したいときは、レジストリエントリを分ける
     *
     * @param string $identifier
     * @param string $id
     * @param string|null $action
     * @return array [array responses, array exceptions]
     */
    public function notify($identifier, $id, $action = null)
    {
        $entries = $this->getInfo($identifier, $id, $action);
        if (empty($entries)) {
            return array(
                'responses' => array(),
                'exceptions' => array(
                    new RuntimeException('entries not found'),
                ),
            );
        }

        $eventPluginManager = $this->getEventPluginManager();
        $exceptions = array();
        $responses = array();

        foreach ($entries as $entry) {
            if (!isset($entry['name'])) {
                continue;
            }

            try {
                $event = $eventPluginManager->get($entry['name'], $entry['params']);
                $responses[] = $this->triggerIdentifier($entry['name'], $event);
            } catch (\Exception $ex) {
                $exceptions[] = $ex;
                continue;
            }
        }

        return array(
            'responses' => $responses,
            'exceptions' => $exceptions,
        );
    }

    public function triggerIdentifier($identifier, EventInterface $event)
    {
        $responses = new ResponseCollection;
        $sharedManager = $this->getSharedEventManager();

        //Add wildcard id to the search, if not already added
        $identifiers = array('*', $identifier);

        if (!in_array('*', $identifiers)) {
            $identifiers[] = '*';
        }
        $sharedListeners = array();

        foreach ($identifiers as $id) {
            if (!$listeners = $sharedManager->getListeners($id, $event->getName())) {
                continue;
            }

            if (!is_array($listeners) && !($listeners instanceof Traversable)) {
                continue;
            }

            foreach ($listeners as $listener) {
                if (!$listener instanceof CallbackHandler) {
                    continue;
                }
                $sharedListeners[] = $listener;
            }
        }

        foreach ($sharedListeners as $listener) {
            $listenerCallback = $listener->getCallback();

            // Trigger the listener's callback, and push its result onto the
            // response collection
            $responses->push(call_user_func($listenerCallback, $event));

            // If the event was asked to stop propagating, do so
            if ($event->propagationIsStopped()) {
                $responses->setStopped(true);
                break;
            }
        }

        return $responses;
    }

    public function setEventPluginManager(Event\EventPluginManager $eventPluginManager)
    {
        $this->eventPluginManager = $eventPluginManager;
    }

    public function getEventPluginManager()
    {
        return $this->eventPluginManager;
    }

    public function setRegistry(GatewayInterface $registry)
    {
        $this->registry = $registry;
    }

    public function getRegistry()
    {
        return $this->registry;
    }

    public function getInfo($identifier, $id, $action = null)
    {
        $name = $identifier . '/' . $id;
        if (!empty($action)) {
            $name .= '.' . $action;
        }
        $info = $this->getRegistry()->read($name);
        if (! is_array($info)) {
            return;
        }
        return $info;
    }

    public function getSharedEventManager()
    {
        if (!isset($this->sharedEventManager)) {
            if (!$eventManager = $this->getEventManager()) {
                return;
            }
            $this->sharedEventManager = $eventManager->getSharedManager();
        }

        return $this->sharedEventManager;
    }

}
