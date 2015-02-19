<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl\AuthClient;

use Flower\AccessControl\Exception\RuntimeException;
use Flower\Resource\ResourceClass\Resource;

/**
 *
 * @see Flower\Resource
 * @author tomoaki
 */
class AuthClientResource extends Resource {
    
    protected $type = 'authClient';
    
    protected $identity;
    
    public function __construct($creationOptions = null)
    {
        parent::__construct($creationOptions);
    }
    
    public function setIdentity($identity = null)
    {
        if (null === $identity) {
            switch (true) {
                case isset($this->identity):
                    $identity = $this->identity;
                    break;
                case isset($this->properties['identity']):
                    $identity = $this->properties['identity'];
                    break;
                case isset($this->data->identity):
                    $identity = $this->data->identity;
                    break;
                default:
                    throw new RuntimeException('identity not found');
            }
        }
            
        $this->properties['identity'] = $identity;
        $this->identity = $identity;
        $this->innerId = strtoupper(dechex(sprintf("%u", crc32($identity))));
    }
    
    public function getIdentity()
    {
        if (!isset($this->identity)) {
            $this->setIdentity();
        }
        
        return $this->identity;
    }
    
    public function getInnerId() {
        if (!isset($this->innerId)) {
            $this->setIdentity();
        }
        return $this->innerId;
    }

    public function getResourceId() {
        if (isset($this->resourceId)) {
            return $this->resourceId;
        }
        $this->resourceId = $this->getType() . self::$delimiter . $this->getInnerId();
        return $this->resourceId;
    }

    public function getType() {
        return $this->type;
    }

    /**
     * オブジェクトでシリアライズにjsonを使う場合、Zend\Json\Jsonの戻し対象で
     * クラス名プロパティ等で目的のクラスを使うように、個別に対応した方がよい。
     * 考えられる復元手段が多様に考えられるのでここでは扱わない。
     * 
     * @param type $data
     * @param type $serialized
     * @return type
     * @throws RuntimeException
     */
    public function setData($data, $serialized = false) {
        if ($serialized || is_string($data)) {
            $data = $this->unserialize($data);
        }
        if (!is_object($data)) {
            throw new RuntimeException(__METHOD__ . ' param should be object or serialized object');
        }
        $this->data = $data;
    }

    /**
     * In normal use, $type is not type of var but entity class
     * In this class indicates generic use of global resource
     * 
     * @return string
     */
    public function toString() {
        if (method_exists($this->data, 'toString')) {
            return $this->data->toString();
        }
        
        return \Zend\Json\Json::encode($this->data);
    }
    
    public function __toString()
    {
        return $this->toString();
    }
}
