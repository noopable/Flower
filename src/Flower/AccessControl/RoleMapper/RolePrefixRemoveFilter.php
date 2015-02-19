<?php

/*
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\RoleMapper;

/**
 * 保存されているロール名のPrefixをチェックし、Prefixのあるものだけを返す
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RolePrefixRemoveFilter extends RolePrefixFilter{

    public function filterWithPrefix(array $roles)
    {
        $pattern = '/' . preg_quote($this->prefix . $this->delimiter, '/') . '(.+)/i';
        return array_filter(
            array_map(function($role) use ($pattern) {
                $matches = array();
                if (preg_match($pattern, $role, $matches)) {
                    return $matches[1];
                } else {
                    return false;
                }
            },
            $roles)
        );
    }

}
