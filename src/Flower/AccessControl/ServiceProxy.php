<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl;

use Flower\AccessControl\Exception\RuntimeException;
use Flower\AccessControl\AclAwareInterface;
use Flower\AccessControl\RoleAwareInterface;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Resource\ResourceInterface;
use Zend\Permissions\Acl\Role\RoleInterface;
/**
 * ResourceInterfaceを実装したサービスへのアクセスコントロールを行う
 * サービスオブジェクトはResourceId
 * methodPrivilegeMapで、サービスの公開メソッドに権限名を割り当てる。
 * 設定されていない場合は当該リソースへの全権を持っていないとProxyを拒否する
 * 
 * サービスオブジェクトがアクセス制御情報が欲しいときは、AccessControlServiceProxy
 * を受け取るようにする。
 * ただし、DIなどの自動解決で別のリソース用のProxyを受け取らないように注意。
 * 
 * 注) 
 * ・サービスの実行を目的としており引数の参照はサポートしない。
 * ・method_existsの使用時は注意
 *
 * @author tomoaki
 */
class ServiceProxy {
    
    /**
     *
     * @var Acl
     */
    protected $acl;
    
    /**
     *
     * @var ResourceInterface
     */
    protected $resource;
    
    /**
     *
     * @var RoleInterface
     */
    protected $role;
    
    /**
     * key: method name
     * value: privilege
     * 
     * no key claims all privileges
     * 
     * @var array
     */
    protected $methodPrivilegeMap = array();
    
    /**
     * ResourceInterfaceを実装したサービスオブジェクトに対するアクセス制御プロキシ
     * isAllowedで確認してから実行することが望ましい
     * 
     * @param \Zend\Permissions\Acl\Acl $acl
     * @param \Zend\Permissions\Acl\Resource\ResourceInterface $resource
     * @param \Zend\Permissions\Acl\Role\RoleInterface $role
     * @param array $methodPrivilegeMap
     */
    public function __construct(Acl $acl, ResourceInterface $resource, RoleInterface $role = null, array $methodPrivilegeMap = array())
    {
        $this->acl = $acl;
        $this->resource = $resource;
        $this->role = $role;
        $this->methodPrivilegeMap = $methodPrivilegeMap;
        
        if ($this->resource instanceof AclAwareInterface) {
            $this->resource->setAcl($acl);
        }
        
        if ($this->resource instanceof RoleAwareInterface) {
            $this->resource->setRole($role);
        }
    }
    
    /**
     * 
     * 
     * @param string $privilege target method or privilege
     * @param boolean $usePrivilegeMap use PrivilegeMap or not
     * @return boolean
     */
    public function isAllowed($privilege = null, $usePrivilegeMap = false)
    {
        if ((bool) $usePrivilegeMap) {
            if (isset($this->methodPrivilegeMap[$privilege])) {
                $privilege = $this->methodPrivilegeMap[$privilege];
            }
        }
        
        if (method_exists($this->resource, 'isAllowed')) {
            return call_user_func_array(array($this->resource, 'isAllowed'), func_get_args());
        }
        
        return $this->acl->isAllowed($this->role, $this->resource, $privilege);
    }
    
    /**
     * 
     * @return ResourceInterface & the original service object
     */
    public function passThrough()
    {
        return $this->resource;
    }
    
    /**
     * 
     * @param type $name
     * @param type $arguments
     * @return type
     * @throws RuntimeException
     */
    public function __call($name, $arguments)
    {
        if (!$this->isAllowed($name, true)) {
            throw new RuntimeException('Access not permitted. Check privilege with isAllowed before invoke');
        }
        return call_user_func_array(array($this->resource, $name), $arguments);
    }
}
