<?php

/*
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */
namespace Flower\Resource\ResourceClass;


use Flower\Resource\Exception\RuntimeException;
use Flower\Resource\Exception\InvalidKeyNameException;
use Flower\Resource\ResourceConfig;
/**
 * Description of Resource
 *
 * @author tomoaki
 */
class Resource implements ResourceInterface {

    protected $resourceId;
    
    protected static $delimiter = '_';
    
    protected $substitude = '-';
    
    protected $options = array();
    
    protected $data;
    
    protected $innerId;
    
    protected $properties = array();
    
    protected $type;
    
    public function __construct($creationOptions = null)
    {
        $config = new ResourceConfig($creationOptions);
        $config->configure($this);
    }
    
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function getData($serialize = false) {
        if ($serialize) {
            return $this->serialize($this->data);
        }
        return $this->data;
    }

    public function getInnerId() {
        return $this->innerId;
    }

    public function getProperties() {
        return $this->properties;
    }

    public function setResourceId($resourceId)
    {
        if (! $this->validateKey($resourceId)) {
            throw new InvalidKeyNameException('resourceId is invalid (' . (string) $resourceId . ')');
        }
        $this->resourceId = $resourceId;
        return $this;
    }
    
    public function getResourceId() {
        if (!isset($this->resourceId)) {
            //don't set to $this->resourceId
            return $this->getType() . self::$delimiter . $this->getInnerId();
        }
        return $this->resourceId;
    }

    public function getType() {
        return $this->type;
    }

    public function setData($data, $serialized = false) {
        if ($serialized) {
            $data = $this->unserialize($data);
        }
        $this->data = $data;
        return $this;
    }

    public function setProperties(array $properties) {
        $this->properties = $properties;
        return $this;
    }

    public function setType($type) {
        if (! $this->validateKey($type)) {
            throw new InvalidKeyNameException('type is invalid (' . (string) $type . ')');
        }
        $this->type = (string) $type;
        return $this;
    }

    /**
     * In normal use, $type is not type of var but entity class
     * In this class indicates generic use of global resource
     * 
     * @return string
     */
    public function toString() {
        return $this->serialize($this->data);
    }
    
    public function __toString()
    {
        return $this->toString();
    }
    
    protected function serialize($data, $policy = null)
    {
        if (null === $policy) {
            $policy = isset($this->options['serialize_policy']) ? $this->options['serialize_policy'] : 'json';
        }
        switch ($policy) {
            case 'php':
                $data = serialize($data);
                break;
            case 'json':
                $data = \Zend\Json\Json::encode($data);
                break;
            case 'cast':
                $data = (string) $data;
                break;
            default:
                throw new RuntimeException($policy . ' is unrecognized');
        }
        return $data;
    }
    
    protected function unserialize($data, $policy = null)
    {
        if (null === $policy) {
            $policy = isset($this->options['serialize_policy']) ? $this->options['serialize_policy'] : 'php';
        }
        switch ($policy) {
            case 'php':
                $data = unserialize($data);
                break;
            case 'json':
                $data = \Zend\Json\Json::decode($data);
                break;
            default:
                throw new RuntimeException($policy . ' is unrecognized');
        }
        return $data;
    }

    public function setInnerId($innerId)
    {
        $innerId = (string) $innerId; // int is safe
        if (! $this->validateKey($innerId)) {
            throw new InvalidKeyNameException('innerId is invalid (' . (string) $innerId . ')');
        }
        $this->innerId = (string) $innerId;
        return $this;
    }

    protected function validateKey($value, $type = null)
    {
        if (!is_string($value)) {
            return false;
        }

        switch ($type) {
            case 'type':
            case 'innerId':
                if (false !== strpos($value, self::getDelimiter())) {
                    return false;
                }
                break;
            case 'resourceId':
                if (substr_count($value, self::getDelimiter()) > 1) {
                    return false;
                }
            default:
        }
        return true;
    }
    
    public static function getDelimiter()
    {
        return self::$delimiter;
    }
}
