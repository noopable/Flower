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
class ScalarStringInnerId extends Resource implements WakeUpInterface {

    /**
     * 標準のリソースが持つserialize unserializeを使わない理由は、
     * dataをスカラで保存することで他のキャッシュクライアントで値をそのまま
     * 使えるようにするため
     */
    public function wakeup()
    {
        if (isset($this->properties['serialized'])) {
            $this->data = unserialize($this->properties['serialized']);
        }
    }
    
    public function getInnerId() 
    {
        if (isset($this->innerId)) {
            //specified innerId is this class's main purpose
            return $this->innerId;
        }
        //innerIDが後から設定される可能性があるのでキャッシュしない。
        return md5($this->data);
    }

    public function getResourceId() {
        if (isset($this->resourceId)) {
            //specified resourceId is return
            return $this->resourceId;
        }
        //no cache if cache and it changed we will get wrong resourceId
        return self::culculateResourceId($this->getType(), $this->getInnerId());
    }
    
    public static function culculateResourceId($type, $innerId)
    {
        return $type . self::$delimiter . sprintf("%u", crc32($innerId));
    }

    public function getType() {
        return 'scalar';
    }

    
    public function setData($data, $serialized = false) {
        if (!is_scalar($data)) {
            throw new RuntimeException(__METHOD__ . ' param should be scalar');
        }
        if ($serialized) {
            $data = $this->unserialize($data, 'php');
            $this->properties['serialized'] = $data;
        } else {
            $this->properties['serialized'] = serialize($data);
        }
        $this->data = $data;
        return $this;
    }

    /**
     * In normal use, $type is not type of var but entity class
     * In this class indicates generic use of global resource
     * 
     * @return string
     */
    public function toString() {
        return (string) $this->data;
    }
    
    public function __toString()
    {
        return $this->toString();
    }
}
