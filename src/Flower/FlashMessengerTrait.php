<?php
namespace Flower;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\ServiceManager\ServiceLocatorAwareTrait;
/**
 * Description of FlashMessengerTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class FlashMessengerTrait {
    use ServiceLocatorAwareTrait;

    public function getFlashMessenger()
    {
        if (!isset($this->serviceLocator)) {
            throw new Exception\RuntimeException('ServiceLocator is required in the Dispatch Process');
        }
        $pluginManager = $this->serviceLocator->get('ControllerPluginManager');
        return $pluginManager->get('flashmessenger');
    }
}
