<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\RoleMapper;

/**
 * 保存されているロール名のPrefixをチェックし、Prefixのあるものだけを返す
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RolePrefixFilter {
    
    protected $prefix = 'global';
    protected $delimiter = '.';
    
    public function __invoke(array $roles)
    {
        return $this->filterWithPrefix($roles);
    }
    
    public function filterWithPrefix(array $roles)
    {
        $pattern = '/' . preg_quote($this->prefix . $this->delimiter, '/') . '(.+)/i';
        return array_filter($roles, function ($role) use ($pattern) {return preg_match($pattern, $role);});
    }
    
    public function mapAddPrefix(array $roles)
    {
        return array_map(array($this, 'addPrefixToRole'), $roles);
    }
    
    public function addPrefixToRole($role)
    {
        return $this->prefix . $this->delimiter . $role;
    }
    
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }
    
    public function getPrefix()
    {
        return $this->prefix;
    }
}
