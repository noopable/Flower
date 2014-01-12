<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl;

use Zend\Permissions\Acl\Role\RoleInterface;
/**
 *
 * @author tomoaki
 */
interface RoleAwareInterface {
    public function setRole(RoleInterface $role);
}
