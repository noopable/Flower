<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */
namespace Flower\Resource\ResourceClass;
/**
 * Description of Resource
 *
 * @author tomoaki
 */
class Object extends Resource {

    public function getInnerId() {
        if (isset($this->innerId)) {
            return $this->innerId;
        }
        
        if (method_exists($this->data, 'getInnerId')) {
            $this->innerId = $this->data->getInnerId();
            return $this->innerId;
        }
        $this->innerId = sprintf("%u", crc32($this->toString()));
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
        return get_class($this->data);
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
        return $this->data;
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
