<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl;

use Flower\AccessControl\Exception\RuntimeException;
use Zend\Permissions\Acl\Resource\ResourceInterface;
/**
 * Description of AccessControlledServiceAbstractFactory
 *
 * @author tomoaki
 */
class AccessControlWrapper
{
    protected $service;
    
    public function wrap($instance, array $methodPrivilegeMap = array())
    {
        if (!isset($this->service)) {
            throw new RuntimeException('Access Control Service not found (wrapper needs AccessControlService');
        }
        
        $service = $this->getAccessControlService();
        
        if (! $instance instanceof ResourceInterface) {
            throw new RuntimeException(get_class($instance) . ' doesn\'t implement ResourceInterface');
        }
        
        return new ServiceProxy($service->getAcl(), $instance, $service->getRole(), $methodPrivilegeMap);
    }
    
    public function setAccessControlService(AccessControlService $service)
    {
        $this->service = $service;
    }
    
    public function getAccessControlService()
    {
        return $this->service;
    }

}
