<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl;

use Flower\AccessControl\Exception\RuntimeException;
use Flower\AccessControl\RoleMapper\RoleContainer;
use Flower\ServiceLayer\Wrapper\ServiceWrapperInterface;

use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\Authentication\Adapter\DbTable\AbstractAdapter as DbTableAdapter;
/**
 * Description of Service
 *
 * @author tomoaki
 */
class AccessControlService implements ServiceWrapperInterface{
    use ACSSetterGetterTrait;

    /**
     * 認証を行ったかどうか
     * @var boolean
     */
    protected $isAuthenticated = false;
    
    /**
     *　認証された正規クライアントかどうか
     * @var boolean
     */
    protected $isValidClient;
    
    protected $authResult;
    
    protected $role;
    
    protected $acl;
    
    public function __construct(ServiceConfig $config = null)
    {
        if (null !== $config) {
            $config->configure($this);
        }
    }
    
    /**
     * @todo EventベースにしてStorageでresultを保存する？
     * @return type
     */
    public function authenticate()
    {
        if ($this->isAuthenticated) {
            return;
        }
        
        $authService = $this->getAuthService();
        /* @var \Zend\Authentication\Result */
        $result = $authService->authenticate();
        
        $this->isAuthenticated = true;
        $this->isValidClient = $result->isValid();
        $this->authResult = $result;
        $identity = $result->getIdentity();
        
        if ($this->isValidClient) {
            $role = $this->getRoleMapper()->getRole($identity);
        } else {
            $role = $this->getRoleMapper()->getRole(null);
        }
        
        $this->setRole($role);
        
        $this->injectRoleToAcl($role, $this->getAcl());
        
    }
    
    /**
     * getResultRowObject() - Returns the result row as a stdClass object
     * パスワードカラムを除外したいときは、$omitColumnsに定義する
     * または、リソースストレージのオプションで保存時に指定する。
     *
     * @param  string|array $returnColumns
     * @param  string|array $omitColumns
     * @return stdClass|bool
     */
    public function getAuthResultRowObject($returnColumns = null, $omitColumns = null)
    {
        $adapter = $this->getAuthService()->getAdapter();
        if ($adapter instanceof DbTableAdapter) {
            $res = $adapter->getResultRowObject($returnColumns, $omitColumns);
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
    
    public function getRole()
    {
        //ロールが設定されていればそれを返す。
        if (isset($this->role)) {
            return $this->role;
        }
        
        if (!isset($this->authService)) {
            throw new RuntimeException('there is no role and authentication service');
        }
        
        $this->authenticate();

        return $this->role;
    }
    
    public function setAcl(Acl $acl)
    {
        if (isset($this->role)) {
            $this->injectRoleToAcl($this->role, $acl);
        }
        $this->acl = $acl;
    }
    
    public function getAcl()
    {
        if (!isset($this->acl)) {
            if (!isset($this->aclLoader)) {
                throw new RuntimeException('there are not acl nor aclLoader');
            }
            $this->acl = $this->aclLoader->load();
            if (isset($this->role)) {// not getRole()
                $this->injectRoleToAcl($this->role, $this->acl);
            }
        }
        return $this->acl;
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
    
    public function wrap($instance, $name = null)
    {
        if (null === $name) {
            $name = get_class($instance);
        }
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
