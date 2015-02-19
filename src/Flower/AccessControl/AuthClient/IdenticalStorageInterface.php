<?php

/**
 * 
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\AuthClient;

use Zend\Authentication\Storage\StorageInterface as ZendStorage;
/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface IdenticalStorageInterface extends ZendStorage {
    public function setIdentity($identity);
    public function getIdentity();
}
