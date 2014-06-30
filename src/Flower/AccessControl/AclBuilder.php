<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl;

use Zend\Permissions\Acl\Acl as ZendAcl;

/**
 * Description of Builder
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class AclBuilder
{

    protected $acl;

    protected $aclLoader;

    protected $genericRoles = array();

    protected $genericResources = array();

    protected $genericRules = array();

    /**
     * Acl育成を先に行うことを要求します。
     * 他の便利機能を盛り込まないでください。
     *
     * @param \Zend\Permissions\Acl\Acl $acl
     */
    public function __construct(ZendAcl $acl = null)
    {
        //acl育成を先に行うことを要求します。
        $this->acl = $acl;
    }

    public function setAcl(ZendAcl $acl)
    {
        $this->acl = $acl;
    }

    public function setGenerics($genericRoles, $genericResources, $genericRules)
    {
        $this->genericRoles = $genericRoles;

        $this->genericResources = $genericResources;

        $this->genericRules = $genericRules;
    }

    public function setGenericRoles($genericRoles)
    {
        $this->genericRoles = $genericRoles;
    }

    public function setGenericResources($genericResources)
    {
        $this->genericResources = $genericResources;
    }

    public function setGenericRules($genericRules)
    {
        $this->genericRules = $genericRules;
    }

    public function build($property, $roles, $resources, $rules)
    {
        $this->addResources($resources, $property);

        $this->addRoles($roles, $property);

        $this->addRules($rules, $property);
    }

    public function addResources(array $resources, $property)
    {
        $acl = $this->acl;
        $props = explode('.', $property);
        $leaf = array_pop($props);
        $parentProperty = implode('.', $props);
        $property = strlen($property) ? rtrim($property, '.') . '.' : '';
        foreach ($resources as $k => $v) {
            $resourceName = null;
            $parentResource = null;
            if (is_int($k)) {
                $resourceName = $property . $v;
                /**
                 * 垂直方向のプロパティ継承を利用する。
                 * これは、document => newDocument などのようなリソース継承とは異なり、
                 * global.document => group.document のような垂直方向の継承となる。
                 */
                if (strlen($parentProperty) > 0) {
                    $parentResource = $parentProperty . '.' . $v;
                } else {
                    $parentResource = $v; // global resource
                }
            } else {
                //同一プロパティ内で継承する
                $resourceName = $property . $k;
                $parentResource = $property . $v;
            }
            if ($acl->hasResource($resourceName)) {
                continue;
            }

            if ($parentResource && $acl->hasResource($resourceName)) {
                $acl->addResource($resourceName, $parentResource);
            } else {
                $acl->addResource($resourceName);
            }
        }
    }

    /**
     * 親プロパティのロールを引き継ごうなどとは思わないように。
     * 基本的に、権限が低くデフォルト許可属性を引き継ぐのがロール継承
     *
     * @param array $roles
     * @param type $property
     */
    public function addRoles(array $roles, $property)
    {
        $property = strlen($property) ? rtrim($property, '.') . '.' : '';
        $acl = $this->acl;
        foreach ($roles as $k => $v) {
            if (is_int($k) && is_string($v)) {
                $acl->hasRole($property . $v)
                        or $acl->addRole($property . $v);
                continue;
            }

            if (is_string($v)) {
                $v = (array) $v;
            }
            $v = array_map(function($role) use ($property) {
                if (strpos($role, '/') === 0) {
                    return substr($role, 1);
                }
                return $property . $role;
            }, $v);
            $acl->hasRole($property . $k)
                    or $acl->addRole($property . $k, $v);
        }
    }

    public function addRules(array $rules)
    {
        $acl = $this->acl;
        foreach ($rules as $rule) {
            if (is_string($rule[1])) {
                $rule[1] = $property . $rule[1];
                if (!$acl->hasRole($rule[1])) {
                    continue;
                }
            }
            if (is_string($rule[2])) {
                $rule[2] = $property . $rule[2];
                $rule[2] = (array) $rule[2];
                foreach ($rule[2] as $role) {
                    if (!$acl->hasResource($role)) {
                        continue 2;
                    }
                }
            }
            array_unshift($rule, $acl::OP_ADD);
            call_user_func_array(array($acl, 'setRule'), $rule);
        }
    }
}
