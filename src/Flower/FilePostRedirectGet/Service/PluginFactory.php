<?php
namespace Flower\FilePostRedirectGet\Service;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\FilePostRedirectGet\Plugin\FilePostRedirectGet;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Description of Factory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PluginFactory implements FactoryInterface
{
    protected $serviceEntryName = 'Flower\FormPostRedirectGet\Plugin\FilePostRedirectGet';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }
        return $serviceLocator->get($this->serviceEntryName);
    }
}
