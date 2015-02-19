<?php

/*
 * 
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\AuthClient;


use Flower\AccessControl\AccessControlService;
use Flower\Resource\Manager\ManagerInterface;

/**
 * identity 以外に個別クライアント依存プロパティを保持しないようにしてください。
 * 
 * @see Flower\Resource
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ResourceStorage implements ResourceStorageInterface {
    
    protected $identity;
    
    protected $service;
    
    protected $resourceManager;
    
    public function __construct(AccessControlService $service)
    {
        $this->service = $service;
    }
    
    public function setAccessControlService(AccessControlService $service)
    {
        $this->service = $service;
    }
    
    public function getAccessControlService()
    {
        return $this->service;
    }
    
    public function setIdentity($identity)
    {
        $this->identity = (string) $identity;
    }
    
    public function getIdentity()
    {
        return $this->identity;
    }
    
    public function getResourceId()
    {
        if (!isset($this->identity)) {
            return;
        }
        
        return $this->getBareBoneClientResource($this->identity)->getResourceId();
    }
    
    public function getCurrentClientResource()
    {
        if (!isset($this->identity)) {
            return;
        }
        return $this->getResourceManager()->get($this->getResourceId());
    }
    
    public function getCurrentClientData()
    {
        $resource = $this->getCurrentClientResource();
        if (! $resource instanceof AuthClientResource) {
            return;
        }
        return $resource->getData();
    }
    
    public function getBareBoneClientResource($identity)
    {
        $resource = new AuthClientResource;
        $resource->setIdentity($identity);
        return $resource;
    }
    
    public function setResourceManager(ManagerInterface $resourceManager)
    {
        $this->resourceManager = $resourceManager;
    }
    
    public function getResourceManager()
    {
        return $this->resourceManager;
    }
    
   /**
    * 認証済み追加データを保存するクラス内で、
    * 認証済みデータが存在するかどうかを確認するメソッド
    * 認証済みidentityを元にデータを取得するリソースストレージでは
    * identityが設定されているかどうかが実質的な問題になりますが、
    * Session Storageより上位に配置した場合は自動的にidentityを取得できる。
    * 
    * そのため認証バイパスは機能しません。セッションを利用してください。
    * 認証を永続化する目的の場合、clearも同時に使用してください。
     * Returns true if and only if storage is empty
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If it is impossible to determine whether storage is empty
     * @return bool
     */
    public function isEmpty()
    {
        //This class feeds no authentication info
        return !isset($this->identity);
    }

    /**
     * read - write compatibility read() returns identity
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If reading contents from storage is impossible
     * @return mixed
     */
    public function read()
    {
        return $this->identity;
    }

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws \Zend\Authentication\Exception\ExceptionInterface If writing $contents to storage is impossible
     * @return void
     */
    public function write($contents)
    {
        if (is_string($contents)) {
            $this->setIdentity($contents);
        }
        /** @var \stdClass */
        $result = $this->service->getAuthResultRowObject();
        if (is_object($result)) {
            $resource = $this->getBareBoneClientResource($contents);
            $resource->setData($result);
            $this->getResourceManager()->saveResource($resource);
        }
    }

    /**
     * Clears contents from storage
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If clearing contents from storage is impossible
     * @return void
     */
    public function clear()
    {
        //no action because always isEmpty is true
    }
}
