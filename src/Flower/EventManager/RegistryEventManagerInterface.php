<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager;

use Flower\EventManager\Event\EventPluginManager;
use Flower\File\Gateway\GatewayInterface;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerAwareInterface;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface RegistryEventManagerInterface extends EventManagerAwareInterface
{
    /**
     * Emit entries in the registry, and map each to triggerIdentifier
     *
     * In the Registry, Entry files are named as the format 'identifier/$id.$action' .extension
     * The entry has parameters for making Event.
     * We should prepare EventPluginManager to get the event by Invokable or AbstractFactory and so on.
     *
     */
    public function notify($identifier, $id, $action = null);

    /**
     * Trigger all listeners in sharedEvents of $identifier for a given event
     *
     * @param  string $identifier identifier of SharedEventManager's children
     * @param  EventInterface $event
     * @return ResponseCollection All listener return values
     * @throws Exception\InvalidCallbackException
     */
    public function triggerIdentifier($identifier, EventInterface $event);

    public function setEventPluginManager(EventPluginManager $eventPluginManager);

    public function getEventPluginManager();

    public function setRegistry(GatewayInterface $registry);

    public function getRegistry();

    public function getSharedEventManager();
}
