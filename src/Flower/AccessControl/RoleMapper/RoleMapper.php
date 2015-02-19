<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl\RoleMapper;

use Flower\AccessControl\AuthClient\ResourceStorageAwareInterface;
use Flower\AccessControl\AuthClient\ResourceStorageAwareTrait;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Permissions\Acl\Role\GenericRole;
/**
 * プロジェクトに合ったRoleMapperを自由に作成してServiceにinjectしてください。
 *
 * @author tomoaki
 */
class RoleMapper implements RoleMapperInterface, ResourceStorageAwareInterface {
    use ResourceStorageAwareTrait;

    /**
     *
     * @var callable
     */
    protected $roleFilter;

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
        $roles = array();

        if (isset($data->role)) {
            if ($data->role instanceof RoleContainer) {
                //たぶんそんなことはないと思いますがｗ
                $role = $data->role;
                $role->addParents(RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT);
                return $role;
            } elseif ($data->role instanceof RoleInterface || is_string($data->role)) {
                $roles = array($data->role);
            }
        } elseif (isset($data->roles)) {
            if (is_string($data->roles)) {
                $roles =array_merge($roles, explode(',', $data->roles));
            } elseif (is_array($data->roles)) {
                $roles = array_merge($roles, $data->roles);
            }
        } elseif (isset($data->roles_serialized)) {
            $roles = array_merge($roles, unserialize($data->roles_serialized));
        }

        $roles = $this->filterRoles($roles);
        $roles[] = RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT;

        if (count($roles) == 1) {
            return new GenericRole(array_shift($roles));
        }

        $role = new RoleContainer(RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE);
        $role->setParents($roles);
        return $role;
    }

    /**
     *
     * @param callable $roleFilter
     */
    public function setRoleFilter($roleFilter)
    {
        $this->roleFilter = $roleFilter;
    }

    /**
     *
     * @return callable
     */
    public function getRoleFilter()
    {
        return $this->roleFilter;
    }

    /**
     *
     * @param array $roles
     * @return array
     */
    public function filterRoles(array $roles)
    {
        if (!isset($this->roleFilter)) {
            return $roles;
        }

        if (!is_callable($this->roleFilter)) {
            return $roles;
        }

        return call_user_func($this->roleFilter, $roles);
    }
}
