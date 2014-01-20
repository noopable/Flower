<?php
/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl;

use Flower\AccessControl\Exception\RuntimeException;
use Flower\AccessControl\RoleMapper\RoleMapper;
use Flower\AccessControl\RoleMapper\RoleMapperInterface;
use Flower\Resource\Manager\ManagerInterface as ResourceManager;
use Zend\Authentication\AuthenticationService;


trait ACSSetterGetterTrait {
    
    /**
     *
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authService;
    
    /**
     *
     * @var RoleMapper
     */
    protected $roleMapper;
    
    /**
     *
     * @var AccessControlWrapper
     */
    protected $accessControlWrapper;
    
    
    /**
     * キー名にサービス取得名としての名前、値にPrivilegeMap配列
     *  　PrivilegeMapはメソッド名 => 権限名
     * 例
     * array('foo' => array('doAction' => 'do'));
     * @var array
     */
    protected $methodPrivilegeMaps;
    
    /**
     * アクセス制御下におきたいサービス名＝＞サービス名のハッシュテーブル
     * 
     * @var array
     */
    protected $underAccessControls;
    
    protected $aclLoader;
    
    protected $aclScriptPath;
    
    protected $resourceManager;
    
    protected $returnColumns;
    
    protected $omitColumns;
    
    public function setAuthService(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }
    
    public function getAuthService()
    {
        return $this->authService;
    }
    
    public function setRoleMapper(RoleMapperInterface $roleMapper)
    {
        $this->roleMapper = $roleMapper;
    }
    
    public function getRoleMapper()
    {
        if (!isset($this->roleMapper)) {
            $this->roleMapper = new RoleMapper;
        }
        return $this->roleMapper;
    }
    
    public function setResourceManager(ResourceManager $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }
    
    public function getResourceManager()
    {
        return $this->resourceManager;
    }
    
    public function setAccessControlWrapper(AccessControlWrapper $accessControlWrapper)
    {
        $this->accessControlWrapper = $accessControlWrapper;
    }
    
    public function getAccessControlWrapper()
    {
        if (!isset($this->accessControlWrapper)) {
            $this->accessControlWrapper = new AccessControlWrapper;
            $this->accessControlWrapper->setAccessControlService($this);
        }
        return $this->accessControlWrapper;
    }
    
    
    public function setMethodPrivilegeMaps(array $methodPrivilegeMaps)
    {
        $this->methodPrivilegeMaps = $methodPrivilegeMaps;
    }
    
    public function addMethodPrivilegeMap($name, $methodPrivilegeMap)
    {
        if (isset($this->methodPrivilegeMaps[$name])) {
            $methodPrivilegeMap = array_merge($this->methodPrivilegeMaps[$name], $methodPrivilegeMap);
        }
        $this->methodPrivilegeMaps[$name] = $methodPrivilegeMap;
    }
    
    public function getMethodPrivilegeMap($name)
    {
        if (!isset($this->methodPrivilegeMaps[$name])) {
            return array();
        }
        return $this->methodPrivilegeMaps[$name];
    }
    
    public function setUnderAccessControls(array $underAccessControls)
    {
        $this->underAccessControls = array();
        foreach ($underAccessControls as $name) {
            $this->addUnderAccessControl($name);
        }
    }
    
    public function getUnderAccessControls()
    {
        return $this->underAccessControls;
    }
    
    public function isUnderAccessControl($name)
    {
        $name = strtolower($name);
        return isset($this->underAccessControls[$name]);
    }
    
    public function addUnderAccessControl($name)
    {
        $name = strtolower($name);
        $this->underAccessControls[$name] = $name;
    }
    
    public function removeUnderAccessControl($name)
    {
        $name = strtolower($name);
        if (isset($this->underAccessControls[$name])) { 
            unset($this->underAccessControls[$name]);
        }
    }
    
    public function setAclLoader(AclLoader $aclLoader)
    {
        $this->aclLoader = $aclLoader;
    }
    
    public function getAclLoader()
    {
        if (!isset($this->aclLoader)) {
            if (!isset($this->aclScriptPath)) {
                throw new RuntimeException('Standard AclLoader needs aclScriptPath but not set');
            }
            $this->aclLoader = new AclLoader($this->aclScriptPath);
        }
        return $this->aclLoader;
    }
    
    public function setAclScriptPath($aclScriptPath)
    {
        $this->aclScriptPath = $aclScriptPath;
    }
    
    public function getAclScriptPath()
    {
        return $this->aclScriptPath;
    }
    
    public function setAuthResultReturnColumns($returnColumns)
    {
        $this->returnColumns = $returnColumns;
    }
    
    public function setAuthResultOmitColumns($omitColumns)
    {
        $this->omitColumns = $omitColumns;
    }
}

