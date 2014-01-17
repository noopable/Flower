<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\Model\Service\TestAsset;

use Flower\Model\Service\RepositoryPluginManager;
/**
 * Description of HookByNameRepositoryPluginManager
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class HookByNameRepositoryPluginManager extends RepositoryPluginManager{

    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        return func_get_args();
    }
}
