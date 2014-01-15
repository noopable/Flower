<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl;

use Zend\Stdlib\ArrayUtils;
/**
 * Description of ServiceConfig
 *
 * @author tomoaki
 */
class ServiceConfig {
    
    protected $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function configure(AccessControlService $service)
    {
        $config = $this->config; //for convenient
        
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
        return $service;
    }
}
