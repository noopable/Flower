<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Model;

trait RepositoryPluginManagerAwareTrait
{
    protected $repositoryPluginManager;

    public function setRepositoryPluginManager(Service\RepositoryPluginManager $repositoryPluginManager)
    {
        $this->repositoryPluginManager = $repositoryPluginManager;
    }

    public function getRepositoryPluginManager()
    {
        return $this->repositoryPluginManager;
    }
}
