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
class Scalar extends Resource implements WakeUpInterface {

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
            return $this->innerId;
        }
        //有効な使い道があるとは思えないが暫定実装しておく
        //識別したい名前を指定することで保存する意義がある
        $this->innerId = sprintf("%u", crc32(serialize($this->data)));
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
