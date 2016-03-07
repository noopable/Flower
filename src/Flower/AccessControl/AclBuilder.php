<?php

/**
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
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

    public function propTreeBuild ($leafProperties, $roles, $resources, $rules, $resourceInheritVertical = true, $rootIsNullString = true)
    {
        if ($rootIsNullString) {
            $this->build('', $roles, $resources, $rules, $resourceInheritVertical);
        }

        $loadedProperties = [];
        foreach ($leafProperties as $v) {
            $property = '';
            $tree = explode('.', $v);
            do {
                $property .= array_shift($tree);
                if (isset($loadedProperties[$property])) {
                    continue;
                }
                $loadedProperties[$property] = $property;
                $this->build($property, $roles, $resources, $rules, $resourceInheritVertical);

            } while(count($tree) && $property .= '.');
        }
    }

    public function build($property, $roles, $resources, $rules, $resourceInheritVertical = true)
    {
        $this->addResources($resources, $property, $resourceInheritVertical);

        $this->addRoles($roles, $property);

        $this->addRules($rules, $property);
    }

    public function addResources(array $resources, $property, $inheritVertical = true)
    {
        $this->addPropertyResource($property);
        $acl = $this->acl;
        $props = explode('.', $property);
        $leaf = array_pop($props);
        $parentProperty = implode('.', $props);
        $parentProperty = strlen($parentProperty) ? $parentProperty . '.' : '';
        $property = strlen($property) ? rtrim($property, '.') . '.' : '';
        foreach ($resources as $k => $v) {
            $resourceName = null;
            $parentResource = null;
            if (is_int($k)) {
                $resourceName = $property . $v;
                if ($inheritVertical) {
                    /**
                     * 垂直方向のプロパティ継承を利用する。
                     * これは、document => newDocument などのようなリソース継承とは異なり、
                     * global.document => group.document のような垂直方向の継承となる。
                     */
                    if (strlen($parentProperty) > 0) {
                        $parentResource = $parentProperty . $v;
                    } else {
                        $parentResource = $v; // global resource
                    }
                } else {
                    /**
                     * 水平方向のプロパティ継承
                     *   通常、親プロパティの権限者でも下位プロパティのリソースを操作するべきではない。
                     * 　ただし、当該プロパティに対する全権を持っている時を除く。
                     * group => group.document
                     *
                     */
                    $parentResource = rtrim($property, '.');
                }
            } else {
                //同一プロパティ内で継承する
                $resourceName = $property . $k;
                $parentResource = strlen($v) ? $property . $v : rtrim($property, '.');
            }
            if ($acl->hasResource($resourceName)) {
                continue;
            }

            if ($parentResource && $acl->hasResource($parentResource)) {
                $acl->addResource($resourceName, $parentResource);
            } else {
                $acl->addResource($resourceName);
            }
        }
    }

    public function addPropertyResource($property)
    {
        /**
         *
         * 既に追加されている。
         *
         */
        if ($this->acl->hasResource($property)) {
            return true;
        }

        $props = explode('.', $property);
        $leaf = array_pop($props);

        if (count($props) > 0) {
            $parentProperty = implode('.', $props);

            if ($this->addPropertyResource($parentProperty)) {
                $this->acl->addResource($property, $parentProperty);
                return true;
            }
        } else {
            $this->acl->addResource($property);
            return true;
        }
        //throw exception?
        return false;
    }

    /**
     * 親プロパティのロールを引き継ごうなどとは思わないように。
     * 子プロパティのロールで親プロパティへの権限が付与されてはならない。
     *
     * 基本的に、権限が低くデフォルト許可属性を引き継ぐのがロール継承
     * 継承しようとしているロールが先に登録されていない場合は例外が投げられます。
     *
     * @param array $roles
     * @param type $property
     * @throws Zend\Permissions\Acl\Exception\InvalidArgumentException
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

            /**
             *
             * ロール継承を含むロール追加
             *
             * キー => 配列
             * 　キーがロール名で配列が親ロールになります。
             *
             */
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

    /**
     * プロパティローカルのロールとリソースを必要とします。
     *
     * @param array $rules
     * @param type $property
     */
    public function addRules(array $rules, $property)
    {
        $property = strlen($property) ? rtrim($property, '.') . '.' : '';
        $acl = $this->acl;
        foreach ($rules as $rule) {
            //ルールの追加を指定
            //$rule[0] add
            array_unshift($rule, $acl::OP_ADD);
            //$rule[1]
            // allow or deny
            //フィルタする？

            //roleの確認
            if (is_string($rule[2])) {
                $rule[2] = $property . $rule[2];
                if (!$acl->hasRole($rule[2])) {
                    continue;
                }
            }
            //リソースの確認
            if (is_string($rule[3])) {
                $rule[3] = (array) $rule[3];
            }

            if (is_array($rule[3])) {
                $resources = array_map(
                    function($resource) use ($property) {
                        if (null === $resource) {
                            return rtrim($property, '.');
                        }
                        return $property . $resource;
                    },
                    $rule[3]
                );

                $rule[3] = $resources;
            } elseif (null === $rule[3] && strlen($property) > 0) {
                $rule[3] = rtrim($property, '.');
            }

            //$rule[4] privileges
            //

            //$rule[5] assertion
            /**
             * AssertionInterface
             * assertionはaddRule時に評価される。
             * aclをキャッシュするとassertionは作成時のデータが利用される。
             */
            $bubbleUp = null;
            if (isset($rule[6])) {
                $bubbleUp = $rule[6];
            }

            //ルールの追加を実行
            call_user_func_array(array($acl, 'setRule'), $rule);

            // $rule[6] special bubble up to property usage
            if (null !== $bubbleUp) {
                if (is_string($bubbleUp)) {
                    $bubbleUp = [$bubbleUp];
                }
                $rule[4] = $bubbleUp;
                $props = explode('.', rtrim($property, '.'));
                $rule[3] = [];
                do {
                    $rule[3][] = implode('.', $props);
                    array_pop($props);
                } while(count($props));
                call_user_func_array(array($acl, 'setRule'), $rule);
            }
        }
    }
}
