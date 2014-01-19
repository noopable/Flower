<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl\RoleMapper;

use Flower\AccessControl\AuthClient\ResourceStorageAwareInterface;
use Flower\AccessControl\AuthClient\ResourceStorageAwareTrait;
use Zend\Permissions\Acl\Role\RoleInterface;
/**
 * プロジェクトに合ったRoleMapperを自由に作成してServiceにinjectしてください。
 *
 * @author tomoaki
 */
class RoleMapper implements RoleMapperInterface, ResourceStorageAwareInterface {
    use ResourceStorageAwareTrait;

    
    public function getRole($identity = null)
    {
        if (empty($identity)) {
            return new RoleContainer(RoleMapperInterface::BUILT_IN_NOT_AUTHENTICATED_CLIENT);
        }
        
        if (!isset($this->resourceStorage)) {
            return new RoleContainer(RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT);
        }
        
        $resourceStorage = $this->getResourceStorage();
        $resourceStorage->setIdentity($identity);
        $data = $resourceStorage->getCurrentClientData();
        $roles = array(RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT);
        if (isset($data->role)) {
            if ($data->role instanceof RoleContainer) {
                //たぶんそんなことはないと思いますがｗ
                $role = $data->role;
                $role->addParents(RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT);
                return $role;
            } elseif ($data->role instanceof RoleInterface || is_string($data->role)) {
                $roles[] = $data->role;
            }
        } elseif (isset($data->roles)) {
            if (is_string($data->roles)) {
                $roles = explode(',', $data->roles);
            } elseif (is_array($data->roles)) {
                $roles = array_merge($roles, $data->roles);
            }
        }
            
        $role = new RoleContainer(RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE);
        $role->setParents($roles);
        return $role;
    }

}
