<?php
namespace Flower\Model;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
/**
 *
 */
interface RepositoryPluginManagerAwareInterface {
    
    /**
     * 
     * @param \Flower\Model\Service\RepositoryPluginManager $repositoryPluginManager
     */
    public function setRepositoryPluginManager(Service\RepositoryPluginManager $repositoryPluginManager);
    
    /**
     * 
     * @return \Flower\Model\Service\RepositoryPluginManager
     */
    public function getRepositoryPluginManager();
}

