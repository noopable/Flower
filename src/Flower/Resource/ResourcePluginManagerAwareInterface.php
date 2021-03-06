<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Resource;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ResourcePluginManagerAwareInterface {
    public function setResourcePluginManager(ResourcePluginManager $resourcePluginManager);
    public function getResourcePluginManager();
}
