<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer;

use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * 1) via. ServiceLocator
 * Factory等で直接ServiceLocatorに登録して使ってもよい
 * 
 * 2) via. ServiceLayerPluginManager
 * PluginManagerからサービスを取得するとServiceManagerはプラグインマネージャーである。
 * Initializerを登録してAccessControlなどでレイヤー全体に共通の仕様を適用することが可能だろう。
 * サービス間はServiceManager経由で互いに使える。
 * Zend\Mvc環境での使用を想定しており、PluginManager->getServiceLocator()でMVCサービスロケータを取得する。
 * 
 *
 * @author tomoaki
 */
abstract class AbstractService  implements 
    ServiceLayerInterface,
    ResourceInterface, 
    ServiceLocatorAwareInterface {
    use ServiceLocatorAwareTrait;

    protected $resourceId = 'flower_generic_service';
    
    protected $mvcServiceLocator;
    
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;
    }
            
    public function getResourceId()
    {
        return $this->resourceId;
    }
    
    public function getMvcServiceLocator()
    {
        if (!isset($this->mvcServiceLocator)) {
            /* @var AbstractPluginManager|ServiceManager $sl */
            $sl = $this->getServiceLocator();
            
            /* @if @var AbstractPluginManager $sl */
            while ($sl instanceof AbstractPluginManager) {
                /* @var AbstractPluginManager|ServiceManager|null $sl */
                $sl = $sl->getServiceLocator();
            }
            /* @var ServiceManager|null $sl */
            if ($sl instanceof ServiceLocatorInterface) {
                $this->mvcServiceLocator = $sl;
            }
        }

        return $this->mvcServiceLocator;
    }
}
