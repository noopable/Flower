<?php

namespace Flower\Model;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\Resource\AbstractResource;

use Zend\Session\Container;
use ArrayObject;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

abstract class AbstractSessionRepository extends AbstractResource
 implements RepositoryInterface, ServiceLocatorAwareInterface, RepositoryPluginManagerAwareInterface
{
    use ServiceLocatorAwareTrait;
    use RepositoryPluginManagerAwareTrait;
    
    protected $session;

    protected $mappingMethods;

    protected $entityPrototype;

    protected $isInitialized = false;

    /**
     *
     * @param $name
     * @param $entityPrototype
     * @param $namespace
     */
    public function __construct($name = null, $entityPrototype, $namespace = null)
    {
        if (null !== $name) {
            $this->setName($name);
        }
        else {
            throw new \RuntimeException("name が指定されていません");
        }

        $this->entityPrototype = $entityPrototype;

        if (null !== $namespace) {
            $this->setNamespace($namespace);
        }
    }

    public function getSessionName()
    {
        return $this->session->getName();
    }

    public function initialize()
    {
        if ($this->isInitialized) {
            return;
        }
        $this->setSessionContainer();
        //オプションを読み込んで、セッション切れ期間の設定や、
        //remember me forget meなどの処理を実装する。＝＞コントローラーの仕事か？
        $this->isInitialized = true;
    }

    public function isInitialized()
    {
        return $this->isInitialized;
    }
    
    public function setSessionContainer(Container $session = null)
    {
        if (null !== $session) {
            $this->session = $session;
            return;
        }

        if (! isset($this->session) &&($session === null)) {
            $this->session = new Container($this->getNamespace());
            return;
        }
    }

    public function getSession()
    {
        return $this->session;
    }

    public function setNamespace($namespace)
    {
        $this->setOption('namespace', $namespace);
    }

    public function getNamespace($default = 'Default')
    {
        return $this->getOption('namespace', $default);
    }

    public function getEntityPrototype()
    {
        return $this->entityPrototype;
    }

    public function getEntity($key = null)
    {
        if (null === $key) {
            return $this->create();
        }

        if (!isset($this->session->$key)) {
            return null;
        }

        $data = $this->session->$key;
        $prototype = $this->getEntityPrototype();
        if (is_array($data)
            && ($prototype instanceof ArrayObject || method_exists($prototype, 'exchangeArray'))) {
            $entity = clone $prototype;
            $entity->exchangeArray($data);
            return $entity;
        }
        else {
            return $data;
        }
    }

    /**
     * コレクションで独自のイテレータを使いたい場合には、オプションで指定できる。
     * コレクションオブジェクトを別途実装したい場合は、可能ならZend\Session\Containerを継承してsetSessionする。
     *
     */
    public function getCollection()
    {
        if ($iteratorClass = $this->getOption('collectionIterator', false)) {
            $this->session->setIteratorClass($iteratorClass);
        }
        return $this->session->getIterator();
    }

    public function create()
    {
        $prototype = $this->getEntityPrototype();
        return clone $prototype;
    }

    protected function detectEntityId($entity)
    {
        $id = null;

        if (method_exists($entity, 'getIdentifier')) {
            $identifier = $entity->getIdentifier();
            $id = $entity->$identifier;
        }
        elseif (method_exists($entity, 'getId')) {
            $id = $entity->getId();
        }
        elseif (method_exists($entity, 'getName')) {
            $id = $entity->getName();
        }
        else {
            throw new Exception('All entity needs id : TODO: make Exception Class');
        }

        return $id;
    }

    protected function entityToArray($entity)
    {
        switch (true) {
            case ($entity instanceof ArrayObject):
                return $entity->getArrayCopy();
            case is_object($entity):
                if (method_exists($entity, 'getArrayCopy')) {
                    return $entity->getArrayCopy();
                }
                else {
                    return get_object_vars($entity);
                }
            case is_array($entity):
            default:
                return $entity;
        }
    }

    public function save($entity, $id = null)
    {
        if (null === $id) {
            $id = $this->detectEntityId($entity);
        }

        $data = $this->entityToArray($entity);
        $this->session->$id = $data;
    }

    public function deleteEntity($entity)
    {
        $id = $this->detectEntityId($entity);
        return $this->delete($id);
    }

    public function delete($id)
    {
        unset($this->session->$id);
    }

    public function deleteAll()
    {
        foreach ($this->session as $k => $v) {
            unset($this->session->$k);
        }
    }

    public function updateCollection(array $data)
    {
        $target = $data;
        foreach ($this->session as $k => $v) {
            if (! array_key_exists($k, $target)) {
                unset($this->session->$k);
            }
            else {
                $this->session->$k = $this->entityToArray($target[$k]);
                unset($target[$k]);
            }
        }

        if (count($target) > 0) {
            foreach($target as $k => $v) {
                $this->session->$k = $this->entityToArray($v);
            }
        }
    }

    public function deleteCollection(array $data)
    {
        foreach ($data as $k => $v) {
            if (isset($this->session->$k)) {
                unset($this->session->$k);
            }
        }
    }

    /*
     * //backup 2012/10/2 20:41
    public function updateCollection(array $data)
    {
        $target = $data;
        foreach ($this->session as $k => $v) {
            if (! array_key_exists($k, $target)) {
                unset($this->session->$k);
            }
            else {
                $this->session->$k = $target[$k];
                unset($target[$k]);
            }
        }

        if (count($target) > 0) {
            foreach($target as $k => $v) {
                $this->session->$k = $v;
            }
        }
    }
    */

    public function setMappingMethods(array $methods)
    {
        $this->mappingMethods = $methods;
    }

    public function __call($name, $params = null)
    {
        if (isset($this->mappingMethods[$name])
            && is_callable($this->mappingMethods[$name])) {
            if (null === $params) {
                return call_user_func($this->mappingMethods[$name], $this->dao);
            }
            else {
                array_unshift($params, $this->dao);
                return call_user_func_array($this->mappingMethods[$name], $params);
            }
        }
    }

}