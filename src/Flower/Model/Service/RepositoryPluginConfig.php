<?php
namespace Flower\Model\Service;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\Model\RepositoryInterface;

use Zend\ServiceManager\Config;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\Di\DiAbstractServiceFactory;
use Zend\ServiceManager\Di\DiServiceInitializer;

/**
 * Description of RepositoryPluginConfig
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RepositoryPluginConfig extends Config {

    protected $serviceLocator;
    
    /**
     * Constructor
     *
     * Merges internal arrays with those passed via configuration
     *
     * @param  array $configuration
     */
    public function __construct(array $configuration = array()) {
        if (isset($configuration['service_locator'])) {
            $this->serviceLocator = $configuration['service_locator'];
        }
        parent::__construct($configuration);
    }
    
    public function getPluginNameSpace()
    {
        if (isset($this->config['plugin_name_space'])) {
            return $this->config['plugin_name_space'];
        }
    }
    
    /**
     * Configure service manager
     *
     * @param ServiceManager $serviceManager
     * @return void
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        parent::configureServiceManager($serviceManager);
        
        if (($pluginNameSpace = $this->getPluginNameSpace()) !== null) {
            if ($serviceManager instanceof PluginNamespaceInterface) {
                $serviceManager->setPluginNameSpace($pluginNameSpace);
            }
        }
        
        if (isset($this->serviceLocator)) {
            $serviceLocator = $this->serviceLocator;
            /*
            $serviceManager->addInitializer(function ($instance) use ($serviceLocator) {
                if ($instance instanceof ServiceManagerAwareInterface) {
                    $instance->setServiceManager($serviceLocator);
                }
            });

            $serviceManager->addInitializer(function ($instance) use ($serviceLocator) {
                if ($instance instanceof ServiceLocatorAwareInterface) {
                    $instance->setServiceLocator($serviceLocator);
                }
            });

            $serviceManager->setServiceLocator($serviceLocator);
            $serviceManager->setAlias('Zend\ServiceManager\ServiceLocatorInterface', 'ServiceManager');
            $serviceManager->setAlias('Zend\ServiceManager\ServiceManager', 'ServiceManager');
            */
            /**
             * Initializerはデフォルトではスタックされる。
             * インスタンス化した直後にaddInitializerすることで末尾に入れる。
             * ※addInitializerの第2引数をfalseにして末尾に追加することもできる。
             * 念のためfalseにしておく。
             */


            /**
             * abstractFactoryはサービス名のみでDiからインスタンスを呼び出し、
             * サービス化する。
             * これを追加しておくことでDi定義による依存性を自動解決したサービス
             * を構成することができる。
             * ただし、DIによる解決を必要としないなら、invokablesで直接、インスタンス
             * 化したほうがよい。
             * 
             */
            if ($serviceLocator->has('Di')) {
                $di = $serviceLocator->get('Di');
                /**
                 * USE_SL_AFTER_DI DIによる依存解決を期待する。 DI定義をしっかり行う。Factoryを併用してもよい。
                 * USE_SL_BEFORE_DI ServiceLocatorを経由して依存を解決する。＝＞Factoryの実装が必須になる。
                 * USE_SL_NONE DIのみによる解決を行う。
                 * 
                 * ServiceLocatorを通して依存を解決すると、この場合、ServiceLocatorはPluginManagerになり
                 * thisによるインスタンス化、validateが行われる可能性がある。
                 * これは、別クラスのインスタンスを抽入する際に問題になる。
                 * 
                 */
                //$diAbstractServiceFactory = new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_AFTER_DI);
                $diAbstractServiceFactory = new DiAbstractServiceFactory($di, DiAbstractServiceFactory::USE_SL_BEFORE_DI);
                //$diAbstractServiceFactory->instanceManager()->addTypePreference('ServiceManager', $serviceLocator);
                //$diAbstractServiceFactory->instanceManager()->addTypePreference('Zend\ServiceManager\ServiceManager', $serviceLocator);
                $diAbstractServiceFactory->instanceManager()->addTypePreference('Zend\ServiceManager\ServiceLocatorInterface', $serviceLocator);
                
                //$diAbstractServiceFactory->instanceManager()->addTypePreference('RepositoryPluginManager', $serviceManager);
                $diAbstractServiceFactory->instanceManager()->addTypePreference('Flower\Model\Service\RepositoryPluginManager', $serviceManager);
                
                $serviceManager->addAbstractFactory(
                    $diAbstractServiceFactory
                );
                $serviceManager->addInitializer(
                    new DiServiceInitializer($di, $serviceLocator)
                );
            }
        }
        
        $serviceManager->addInitializer(function($instance, $sm) {
            if ($instance instanceof RepositoryInterface) {
                $instance->initialize();
            }
        }
        , false);
    }
}
