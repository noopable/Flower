<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\AccessControl\RoleMapper;
/**
 *
 * @author tomoaki
 */
interface RoleMapperInterface {
    const BUILT_IN_NOT_AUTHENTICATED_CLIENT = 'flower_not-available';
    const BUILT_IN_AUTHENTICATED_CLIENT = 'flower_authenticated';
    const BUILT_IN_CURRENT_CLIENT_AGGREGATE = 'flower_current-client-aggregate';
    
    public function getRole($identity = null);
}
