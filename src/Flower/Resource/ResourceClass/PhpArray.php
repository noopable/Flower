<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

namespace Flower\Resource\ResourceClass;

use Flower\Resource\Exception\RuntimeException;

/**
 * dataとして保存する際はjson
 *      ユニバーサルアクセスに対処
 * serializedにはphpで保存する
 *      phpでのデータの保存性を優先
 *
 * @author tomoaki
 */
class PhpArray extends Resource implements WakeUpInterface {

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

    public function getResourceId()
    {
        if (isset($this->resourceId)) {
            return $this->resourceId;
        }
        $this->resourceId = $this->getType() . self::$delimiter . $this->getInnerId();
        return $this->resourceId;
    }

    public function getType()
    {
        return 'array';
    }

    public function getData($serialize = false)
    {
        if ($serialize) {
            $data = $this->serialize($data, 'json');
        }
        return $this->data;
    }

    public function setData($data, $serialized = false)
    {
        if ($serialized) {
            $data = $this->unserialize($data, 'json');
        }
        if (is_object($data)) {
            if ($data instanceof \ArrayObject
                    || method_exists($data, 'getArrayCopy')) {
                $data = $data->getArrayCopy();
            }
        }
        if (!is_array($data)) {
            throw new RuntimeException(__METHOD__ . ' param should be array');
        }
        try {
            $serializer = new \Zend\Serializer\Adapter\PhpSerialize();
            $this->properties['serialized'] = $serializer->serialize($data);
        } catch (\Zend\Serializer\Exception\ExceptionInterface $e) {
            throw new RuntimeException('data can be set if it is serializable array', 0, $e);
        }

        $this->data = $data;
        return $this;
    }

    /**
     * In normal use, $type is not type of var but entity class
     * 
     * @return string
     */
    public function toString()
    {
        return \Zend\Json\Json::encode($this->data);
    }

    public function __toString()
    {
        return $this->toString();
    }

}
