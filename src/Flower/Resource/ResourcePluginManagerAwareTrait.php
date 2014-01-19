<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Resource;

/**
 * Description of ResourcePluginManagerAwareTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait ResourcePluginManagerAwareTrait {
    /**
     *
     * @var ResourcePluginManager
     */
    protected $pluginManager;
    /**
     * 
     * @param ResourcePluginManager $pluginManager
     */
    public function setResourcePluginManager(ResourcePluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }
    
    /**
     * 
     * @return ResourcePluginManager|null;
     */
    public function getResourcePluginManager()
    {
        return $this->pluginManager;
    }
}
