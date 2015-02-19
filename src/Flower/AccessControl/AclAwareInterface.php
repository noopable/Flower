<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl;

use Zend\Permissions\Acl\Acl;
/**
 *
 * @author tomoaki
 */
interface AclAwareInterface {
    /**
     *
     * @param \Zend\Permissions\Acl\Acl $acl
     */
    public function setAcl(Acl $acl);

    /**
     * @return Acl
     */
    public function getAcl();
}
