<?php

/*
 * 
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\AuthClient;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ResourceStorageAwareInterface {
    public function setResourceStorage(ResourceStorageInterface $resourceStorage);
    public function getResourceStorage();
}
