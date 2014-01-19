<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\AuthClient;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ResourceStorageInterface extends IdenticalStorageInterface {
    public function getCurrentClientResource();
    public function getCurrentClientData();
    public function getResourceId();
}
