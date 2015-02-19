<?php

/*
 * 
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\RoleMapper;

use Zend\Permissions\Acl\Role\GenericRole;
/**
 * Description of RoleContainer
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RoleContainer extends GenericRole{
    protected $parents = array();
    
    public function setParents($parents)
    {
        $this->parents = (array) $parents;
    }
    
    public function addParent($parent)
    {
        $this->parents[] = $parent;
    }
    
    public function getParents()
    {
        return $this->parents;
    }
}
