<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl;

use Flower\AccessControl\AuthClient\IdenticalStorageInterface;
use Flower\AccessControl\AuthClient\ResourceStorage;
use Flower\AccessControl\RoleMapper\RoleMapperInterface;
use Flower\Resource\Manager\ManagerInterface as ResourceManager;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Chain;
use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;
/**
 * Description of ServiceConfig
 *
 * @author tomoaki
 */
class ServiceConfig {
    
    protected $config;
    
    protected $serviceLocator;
    
    public function __construct(array $config)
    {
        if (isset($config['service_locator']) && ($config['service_locator'] instanceof ServiceLocatorInterface)) {
            $this->serviceLocator = $config['service_locator'];
            unset($config['service_locator']);
        }
        $this->config = $config;
    }
    
    public function configure(AccessControlService $service)
    {
        $config = $this->config; //for convenient
        
        if (isset($this->serviceLocator)) {
            $this->configureWithServiceLocator($service);
        }
        
        if (isset($config['acl_script_path'])) {
            $config['acl_path'] = $config['acl_script_path'];
        }
        
        if (isset($config['acl_path'])) {
            $aclScriptPath = $config['acl_path'];
            $service->setAclScriptPath($aclScriptPath);
        }
        
        if (isset($config['acl_loader'])) {
            $aclLoader = $config['acl_loader'];
            if (is_string($aclLoader) && isset($aclScriptPath)) {
                $aclLoader = new $aclLoader($aclScriptPath);
            }
            $service->setAclLoader($aclLoader);
        }
        
        if (isset($this->config['method_privilege_maps'])) {
            if (!ArrayUtils::isHashTable($this->config['method_privilege_maps'], true)) {
                throw new Exception\RuntimeException('method_privilege_maps should be hash array');
            }
            foreach ($this->config['method_privilege_maps'] as $key => $value) {
                $service->addMethodPrivilegeMap($key, $value);
            }
        }

        if (isset($this->config['under_access_controls'])) {
            if (!is_array($this->config['under_access_controls'])) {
                throw new Exception\RuntimeException('under_access_controls should be array of service names');
            }
            foreach ($this->config['under_access_controls'] as $name) {
                $service->addUnderAccessControl($name);
            }
        }
        $this->combineDependencies($service);
        return $service;
    }
    
    public function configureWithServiceLocator(AccessControlService $service)
    {
        if (! isset($this->serviceLocator)) {
            return;
        }
        //for lazy type
        $config = $this->config;
        /* @var \Zend\ServiceManager\ServiceLocatorInterface $sl*/
        $sl = $this->serviceLocator;
        /**
         * ServiceLocatorを使ったAuthServiceのセット
         * 
         */
        if (isset($config['auth_service'])
                && $sl->has($config['auth_service'])) {
            $authService = $sl->get($config['auth_service']);
            $service->setAuthService($authService);
        }
        /**
         * 
         * ServiceLocatorを使ったResourceManagerのセット
         * ResourceManagerは他のモジュールや機能で使用する関係で
         * ServiceLocatorにプロジェクト毎の設定で持っている可能性が高い
         */
        if (isset($config['resource_storage'])) {
            $resourceStorage = $config['resource_storage'];
            if (is_string($resourceStorage)
                && $sl->has($resourceStorage)) {
                //ResourceManagerの注入はSLのFactory等に任せる
                $resourceStorage = $sl->get($resourceStorage);
            } elseif(class_exists($resourceStorage)) {
                $resourceStorage = new $resourceStorage;
            }
            if ($resourceStorage instanceof IdenticalStorageInterface) {
                $service->setResourceStorage($resourceStorage);
            }
        }


        if (isset($config['resource_manager'])) {
            $resourceManager = $config['resource_manager'];
            if (is_string($resourceManager)
                && $sl->has($resourceManager)) {
                $resourceManager = $sl->get($resourceManager);
            }
            if ($resourceManager instanceof ResourceManager) {
                $service->setResourceManager($resourceManager);
            }
        }
    }
    
    public function combineDependencies(AccessControlService $service)
    {
        $authService = $service->getAuthService();
        $resourceStorage = $service->getResourceStorage();
        $resourceManager = $service->getResourceManager();
        $roleMapper = $service->getRoleMapper();
        /**
         * 依存で自動育成してよいものがあれば育成する
         */
        if (isset($resourceManager) && !isset($resourceStorage)) {
            $resourceStorage = new ResourceStorage($service);
            $service->setResourceStorage($resourceStorage);
        }
        
        $this->combileResourceManager($resourceManager, $resourceStorage, $authService, $roleMapper);
        //その他組み合わせたいものがあればメソッドを追加する
    }
    
    /**
     * 共通のリソースマネージャーを使うことを目的とする
     * @param \Flower\Resource\Manager\ManagerInterface $resourceManager
     * @param \Flower\AccessControl\AuthClient\ResourceStorage $resourceStorage
     * @param \Zend\Authentication\AuthenticationService $authService
     */
    public function combileResourceManager(ResourceManager $resourceManager = null, $resourceStorage = null, AuthenticationService $authService = null, RoleMapperInterface $roleMapper = null)
    {
        if (null !== $resourceManager && null !== $resourceStorage) {
            $resourceStorage->setResourceManager($resourceManager);
        }
        if (null != $authService && null !== $resourceStorage) {
            $defaultStorage = $authService->getStorage();
            if ($defaultStorage instanceof Chain) {
                //控えめに。
                $defaultStorage->add($resourceStorage, -1);
            } elseif ($defaultStorage instanceof Session) {
                $storage = new Chain;
                $storage->add($defaultStorage, 1);
                $storage->add($resourceStorage, -1);
                $authService->setStorage($storage);
            }
        }
        if (isset($roleMapper) 
            && method_exists($roleMapper, 'setResourceStorage') 
            && isset($resourceStorage)) {
            $roleMapper->setResourceStorage($resourceStorage);
        }
    }
}
