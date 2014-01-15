<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\AuthClient;


use Flower\AccessControl\AccessControlService;
use Flower\Resource\Manager\ManagerInterface;
use Flower\Resource\ResourceClass\ResourceInterface;

/**
 * identity 以外に個別クライアント依存プロパティを保持しないようにしてください。
 * 
 * @see Flower\Resource
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ResourceStorage implements IdenticalStorageInterface {
    
    protected $identity;
    
    protected $service;
    
    protected $returnColumns;
    
    protected $omitColumns;
    
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
    
    public function setAuthResultReturnColumns($returnColumns)
    {
        $this->returnColumns = $returnColumns;
    }
    
    public function setAuthResultOmitColumns($omitColumns)
    {
        $this->omitColumns = $omitColumns;
    }
    
    
    
   /**
    * 認証済み追加データを保存するクラス内で、
    * 認証済みデータが存在するかどうかを確認するメソッド
    * 認証済みidentityを元にデータを取得するリソースストレージでは常にemptyです。
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
        return true;
    }

    /**
     * ロールmapperはこのメソッドを使いたいでしょう。
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If reading contents from storage is impossible
     * @return mixed
     */
    public function read()
    {
        if (!isset($this->identity)) {
            return;
        }
        $resource = $this->getResourceManager()->get($this->getResourceId());
        if ($resource instanceof ResourceInterface) {
            return $resource->getData();
        }
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
        $result = $this->service->getAuthResultRowObject($this->returnColumns, $this->omitColumns);
        $resource = $this->getBareBoneClientResource($contents);
        $resource->setData($result);
        $this->getResourceManager()->saveResource($resource);
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
