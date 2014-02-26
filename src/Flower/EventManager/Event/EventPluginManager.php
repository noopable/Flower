<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\EventManager\Event;

use Flower\EventManager\Exception\RuntimeException;
use Zend\EventManager\EventInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of EventPluginManager
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class EventPluginManager extends AbstractPluginManager
{
    /**
     * Whether or not to share by default
     *
     * @var bool
     */
    protected $shareByDefault = false;

    public function validatePlugin($plugin)
    {
        if ($plugin instanceof EventInterface) {
            return ;
        }
        throw new RuntimeException(sprintf(
            'Object of type %s is invalid; must implement Zend\EventManager\EventInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }

}
