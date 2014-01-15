<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl\RoleMapper;
/**
 *
 * @author tomoaki
 */
interface RoleMapperInterface {
    const BUILT_IN_NOT_AUTHENTICATED_CLIENT = 'not-available';
    const BUILT_IN_AUTHENTICATED_CLIENT = 'authenticated';
    const BUILT_IN_CURRENT_CLIENT_AGGREGATE = 'current-client-aggregate';
    
    public function getRole($identity = null);
}
