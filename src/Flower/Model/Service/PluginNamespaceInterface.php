<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Model\Service;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface PluginNamespaceInterface {
    
    public function setPluginNameSpace($pluginNameSpace);

    public function getPluginNameSpace();

    /**
     * Retrieve a service from the manager by name
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param  string $name
     * @param  array $options
     * @param  bool $usePeeringServiceManagers
     * @return object
     */
    public function byName($name, $options = array(), $usePeeringServiceManagers = true);
}

