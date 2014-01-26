<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\ServiceLayer;

use Flower\Model\Exception\RuntimeException;
use Flower\Model\RepositoryPluginManagerAwareInterface;
use Flower\Model\RepositoryPluginManagerAwareTrait;
use Flower\Model\RepositoryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
/**
 * Description of AbstractRepoService
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class AbstractRepoService extends AbstractService
    implements RepositoryPluginManagerAwareInterface {
    use RepositoryPluginManagerAwareTrait;

    protected $repository;

    protected $repositoryName;

    public function setRepositoryName($repositoryName)
    {
        $this->repositoryName = $repositoryName;
    }

    public function getRepositoryName()
    {
        return $this->repositoryName;
    }


    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        if (false === $this->repository) {
            return;
        }
        if (!isset($this->repository) && isset($this->repositoryName)) {
            try {
                if ($this->getRepositoryPluginManager()->has($this->repositoryName)) {
                    $this->repository = $this->getRepositoryPluginManager()->get($this->repositoryName);
                } else {
                    $this->repository = $this->getRepositoryPluginManager()->byName($this->repositoryName);
                }
            } catch (ServiceNotFoundException $ex) {
                //腐ってやがる。早すぎたんだ。 or 設定ミス？
                $this->repository = false;
                throw new Exception\RuntimeException('repository name '. $this->repositoryName . ' is not found?', 0, $ex);
            }
        }
        return $this->repository;
    }

    /**
     *
     * @param type $name
     * @param type $arguments
     * @return mixed
     * @throws RuntimeException
     */
    public function __call($name, $arguments)
    {
        if (! $repository = $this->getRepository()) {
            throw new RuntimeException('Repository not found. Check Configuration');
        }
        return call_user_func_array(array($repository, $name), $arguments);
    }
}
