<?php
namespace Flower;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\Exception\RuntimeException;
use Zend\View\Model\ViewModel;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response as HttpResponse;

class Module
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     *
     * @see Flower\File\DefaultResolveListener
     * @return string
     */
    public static function dataDir()
    {
        return __DIR__ . '/data';
    }

    public static function getSalt()
    {
        $path = __DIR__ . '/data/salt.php';
        if (! is_readable($path)) {
            throw new RuntimeException('Please set salt in '. $path);
        }
        return include($path);
    }

    public function getConfig()
    {
        $config =  include __DIR__ . '/config/module.config.php';
        return $config;
    }

    public function getServiceConfig()
    {
        return array(
            /*'factories' => array(
                'Flower\Model\ItemTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table     = new Model\ItemTable($dbAdapter);
                    return $table;
                },
            ),*/
        );
    }

    public function getProvides()
    {
        return array(
            __NAMESPACE__ => array(
                'version' => '0.1.0'
            ),
        );
    }

}
