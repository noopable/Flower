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
     * Attach a listener to an event
     *
     * Allows attaching a callback to an event offered by one or more
     * identifying components. As an example, the following connects to the
     * "getAll" event of both an AbstractResource and EntityResource:
     *
     * <code>
     * $sharedEventManager = new SharedEventManager();
     * $sharedEventManager->attach(
     *     array('My\Resource\AbstractResource', 'My\Resource\EntityResource'),
     *     'getAll',
     *     function ($e) use ($cache) {
     *         if (!$id = $e->getParam('id', false)) {
     *             return;
     *         }
     *         if (!$data = $cache->load(get_class($resource) . '::getOne::' . $id )) {
     *             return;
     *         }
     *         return $data;
     *     }
     * );
     * </code>
     *
     * @param  string|array $id Identifier(s) for event emitting component(s)
     * @param  string $event
     * @param  callable $callback PHP Callback
     * @param  int $priority Priority at which listener should execute
     * @return CallbackHandler|array Either CallbackHandler or array of CallbackHandlers
     */
    public function attachWithId($id, $event, $callback, $priority = 1);

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
