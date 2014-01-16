<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer;

use Flower\ServiceLayer\Events\EventsWrapper;
use Zend\ServiceManager\Config;

/**
 * Description of ManagerConfig
 *
 * @author tomoaki
 */
class ManagerConfig extends Config{
    public function configure(ServiceLayerPluginManager $manager)
    {
        parent::configureServiceManager($manager);
        
        $config = $this->config;
        
        if (isset($config['event_enable'])) {
            /**
             * @todo EventWrapper Configクラスを作って任せるべき
             */
            $eventsWrapper = new EventsWrapper;
            $manager->addServiceWrapper($eventsWrapper);
            $eventManager = $manager->getEventManager();
            $eventsWrapper->setEventManager($eventManager);
            if (isset($config['event_targets']) && is_array($config['event_targets'])) {
                $eventsWrapper->setWrapTargets($config['event_targets']);
            }
            if (isset($config['event_target'])) {
                $eventsWrapper->addWrapTarget($config['event_target']);
            }
        }
    }
}
