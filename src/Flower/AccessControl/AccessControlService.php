<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl;

use Flower\AccessControl\AuthClient\ResourceStorageAwareInterface;
use Flower\AccessControl\AuthClient\ResourceStorageAwareTrait;
use Flower\AccessControl\Exception\RuntimeException;
use Flower\AccessControl\RoleMapper\RoleContainer;
use Flower\AccessControl\RoleMapper\RoleMapperInterface;
use Flower\ServiceLayer\Wrapper\ServiceWrapperInterface;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Adapter\DbTable\AbstractAdapter as DbTableAdapter;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Description of Service
 *
 * @author tomoaki
 */
class AccessControlService implements ServiceWrapperInterface, ResourceStorageAwareInterface {
    use ACSSetterGetterTrait;
    use ResourceStorageAwareTrait;

    /**
     * 認証を行ったかどうか
     * @var boolean
     */
    protected $isAuthenticated = false;

    /**
     * is in authenticated session or not
     * @var boolean
     */
    protected $isLoggedIn;

    /**
     *　認証された正規クライアントかどうか
     * @var boolean
     */
    protected $isValidClient;

    protected $authResult;

    protected $role;

    protected $acl;

    protected $builtInRoles;

    public function __construct(ServiceConfig $config = null)
    {
        if (null !== $config) {
            $config->configure($this);
        }

        $this->builtInRoles = array(
            RoleMapperInterface::BUILT_IN_AUTHENTICATED_CLIENT,
            RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE,
            RoleMapperInterface::BUILT_IN_NOT_AUTHENTICATED_CLIENT,
        );
    }

    /**
     * @todo EventベースにしてStorageでresultを保存する？
     * @return type
     */
    public function authenticate($identity = null, $credential = null)
    {
        if ($this->isAuthenticated) {
            return;
        }

        $authService = $this->getAuthService();
        $adapter = $authService->getAdapter();
        if ($adapter instanceof AbstractAdapter) {
            if (null !== $identity) {
                $adapter->setIdentity($identity);
            }
            if (null !== $credential) {
                $adapter->setCredential($credential);
            }
        }
        try {
            /* @var \Zend\Authentication\Result */
            $result = $authService->authenticate();
        } catch (\Zend\Db\Adapter\Exception\RuntimeException $ex) {
            //接続エラー等
            throw new RuntimeException('DbAdapter:' . $ex->getMessage(), 0, $ex);
        }

        $this->isAuthenticated = true;
        $this->isValidClient = $result->isValid();
        $this->authResult = $result;
        $identity = $result->getIdentity();

        if ($this->isValidClient) {
            $this->isLoggedIn = true;
            $role = $this->getRoleMapper()->getRole($identity);
        } else {
            $role = $this->getRoleMapper()->getRole(null);
        }

        $this->setRole($role);

        $this->injectRoleToAcl($role, $this->getAcl());

        return $this->isValidClient;
    }

    public function isLoggedIn()
    {
        if (isset($this->isLoggedIn)) {
            return $this->isLoggedIn;
        }

        $this->isLoggedIn = false;

        if ($identity = $this->getIdentity()) {
            $role = $this->getRoleMapper()->getRole($identity);
            $this->setRole($role);
            $this->injectRoleToAcl($role, $this->getAcl());

            $this->isLoggedIn = true;
        }

        return $this->isLoggedIn;
    }

    public function getIdentity()
    {
        $authService = $this->getAuthService();
        if (! isset($authService) || ! $authService->hasIdentity()) {
            return;
        }

        return $authService->getIdentity();
    }
    /**
     * $authResult = $authService->authenticate() ;
     * $authResult->(isValid | getIdentity | getMessages | getCode )();
     *
     * @return \Zend\Authentication\Result
     */
    public function getAuthResult()
    {
        return $this->authResult;
    }

    /**
     * getResultRowObject() - Returns the result row as a stdClass object
     * パスワードカラムを除外したいときは、$omitColumnsに定義する
     * または、リソースストレージのオプションで保存時に指定する。
     *
     * @return stdClass|bool
     */
    public function getAuthResultRowObject()
    {
        $adapter = $this->getAuthService()->getAdapter();
        if ($adapter instanceof DbTableAdapter) {
            $res = $adapter->getResultRowObject($this->returnColumns, $this->omitColumns);
            return $res;
        }
    }

    public function setRole(RoleInterface $role)
    {
        $this->role = $role;
        if (isset($this->acl)) {
            $this->injectRoleToAcl($role, $this->acl);
        }
    }

    public function getRole($withAuthenticate = false)
    {
        //ロールが設定されていればそれを返す。
        if (isset($this->role)) {
            return $this->role;
        }

        if ($this->isLoggedIn()) {
            $role = $this->getRoleMapper()->getRole($this->getIdentity());
            $this->setRole($role);
            $this->injectRoleToAcl($role, $this->getAcl());
            return $this->role;
        } elseif ($withAuthenticate) {
            if (!isset($this->authService)) {
                throw new RuntimeException('there is no role and authentication service');
            }
            $this->authenticate();
        } else {
            $role = $this->getRoleMapper()->getRole(null);
            $this->setRole($role);
            $this->injectRoleToAcl($role, $this->getAcl());
        }

        return $this->role;
    }

    public function getCurrentClientData()
    {
        $resourceStorage = $this->getResourceStorage();
        if (null === $resourceStorage) {
            return null;
        }

        $identity = $this->getAuthService()->getIdentity();
        if (null === $identity) {
            return null;
        }
        //be sure
        $resourceStorage->setIdentity($identity);

        return $resourceStorage->getCurrentClientData();
    }

    public function setAcl(Acl $acl)
    {
        $this->injectBuiltInRoles($acl);
        if (isset($this->role)) {
            $this->injectRoleToAcl($this->role, $acl);
        }
        $this->acl = $acl;
    }

    public function getAcl()
    {
        if (!isset($this->acl)) {
            if (!$aclLoader = $this->getAclLoader()) {
                throw new RuntimeException('there are not acl nor aclLoader');
            }
            $this->setAcl($aclLoader->load());
        }
        return $this->acl;
    }

    public function injectBuiltInRoles($acl)
    {
        if (!isset($this->builtInRoles)) {
            return $acl;
        }
        foreach ($this->builtInRoles as $role) {
            if (! $acl->hasRole($role)) {
                $acl->addRole($role);
            }
        }
        return $acl;
    }

    public function injectRoleToAcl($role, $acl)
    {
        if (!$acl->hasRole($role)) {
            if ($role instanceof RoleContainer) {
                $parents = $role->getParents();
                $acl->addRole($role, $parents);
            } else {
                $acl->addRole($role);
            }
        }
    }

    public function wrap($name, $instance)
    {
        if (!$this->isUnderAccessControl($name)) {
            return $instance;
        }
        return $this->getAccessControlWrapper()->wrap($instance, $this->getMethodPrivilegeMap($name));
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * The $role and $resource parameters may be references to, or the string identifiers for,
     * an existing Resource and Role combination.
     *
     * If either $role or $resource is null, then the query applies to all Roles or all Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend\Permissions\Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *
     * If a $privilege is not provided, then this method returns false if and only if the
     * Role is denied access to at least one privilege upon the Resource. In other words, this
     * method returns true if and only if the Role is allowed all privileges on the Resource.
     *
     * This method checks Role inheritance using a depth-first traversal of the Role registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the Role are checked.
     *
     * @param  Resource\ResourceInterface|string    $resource
     * @param  string                               $privilege
     * @return bool
     */
    public function isAllowed($resource = null, $privilege = null)
    {
        $acl = $this->getAcl();
        $role = $this->getRole();
        return $acl->isAllowed($role, $resource, $privilege);
    }
}
