<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource\Converter;

use Zend\Serializer\Adapter\PhpSerialize as Serializer;
use Flower\Resource\Exception\RuntimeException;
use Flower\Resource\ResourceClass\Resource;
use Flower\Resource\ResourceClass\ResourceInterface;
use Flower\Resource\ResourcePluginManager;

/**
 *
 * @author tomoaki
 */
class DefaultConverter implements PersistenceConverterInterface{
    
    /**
     *　resourceIDルールをここで規定することに問題はないか？
     * 運用時、リソースタイプ毎にリソースIDルールを取得してマージしたルールをセットすること。
     * 
     * @var string
     */
    protected $keyPattern = '';
    
    /**
     *
     * @see Flower\Resource\ResourceClass\Resource::getDelimiter
     * @var string
     */
    protected $resourceIdDelimiter = '_';
    protected $propDelimiter = '+';
    protected $resourceTypeShortName = 'type';
    protected $propertyShortName = 'props';
    protected $lastUpdatedShortName = 'time';
    protected $stringShortName = 'str';
    
    public function __construct()
    {
        $this->resourceIdDelimiter = Resource::getDelimiter();
        //@notice keyPatterはManagerで集中管理してsetKeyPatternで受け取るべきかもしれない。
        $this->keyPattern = sprintf('/([a-zA-Z0-9-]+)[%s]([a-zA-Z0-9-]+)(?:[%s]([a-zA-Z]+))*/',
                $this->resourceIdDelimiter,
                $this->propDelimiter);
    }
    /**
     * キーを増やすとRedisでは容量を多く取られ、ファイルシステムでは負荷が大きくなる。
     * 
     * @param type $resourceId
     * @return type
     */
    public function resourceIdToKvKeys($resourceId, $criteria = null) 
    {
        $definitions =  array(
            'serialized' => $resourceId, 
            'string' => $resourceId . $this->propDelimiter . $this->stringShortName,
            'properties' => $resourceId . $this->propDelimiter . $this->propertyShortName,
            'lastUpdated' => $resourceId . $this->propDelimiter . $this->lastUpdatedShortName,
        );
        
        if (null === $criteria) {
            return $definitions;
        }
        
        if (!isset($definitions[$criteria])) {
            return array();
        }
        
        return array($criteria => $definitions[$criteria]);
    }
    
    /**
     * 連想配列をリソースに変換する
     * @param array $kvAssoc
     * @return ResourceInterface
     */
    public function AssocToResource(array $kvAssoc)
    {
        try {
            $type = $kvAssoc[$this->resourceTypeShortName];
            $resource = $this->getResourcePluginManager()->get($type, $kvAssoc);
        } catch (\Zend\ServiceManager\Exception\ServiceNotFoundException $ex) {
            throw new RuntimeException("Couldn\'t create instance for $type. Lack of configuration?");
        }
        
        return $resource;
    }

    /**
     * ストレージから取得したKVペアをリソースリストに変換
     * 
     * @param array $kvPairs
     * @return array ResourceInterface[] assoc
     */
    public function kvPairsToResources(array $kvPairs)
    {
        $assocs = $this->kvPairsToAssocs($kvPairs);
        $resources = array();
        foreach ($assocs as $type => $idassoc) {
            foreach ($idassoc as $innerId => $assoc) {
                try {
                    $resource = $this->AssocToResource($assoc);
                    $resources[$resource->getResourceId()] = $resource; 
                } catch (\Exception $ex) {
                    throw new RuntimeException('catch exception at ' . __METHOD__, 0, $ex);
                }
            }
        }
        return $resources;
    }
    
    public function getKeyPattern() 
    {
        return $this->keyPattern;
    }

    public function setKeyPattern($keyPattern) 
    {
        $this->keyPattern = $keyPattern;
    }
    
    /**
     * 
     * @param type ResourcePluginManager
     * @return \Flower\Resource\Converter\DefaultConverter
     */
    public function setResourcePluginManager(ResourcePluginManager $pluginManager)
    {
        $this->pluginManager = $pluginManager;
        return $this;
    }
    
    /**
     * 
     * @return ResourcePluginManager;
     */
    public function getResourcePluginManager()
    {
        if (!isset($this->pluginManager)) {
            $this->pluginManager = new ResourcePluginManager;
        }
        return $this->pluginManager;
    }
    
    public function resourceToKvPair(ResourceInterface $resource) 
    {
        $resourceId = $resource->getResourceId();
        $keys = $this->resourceIdToKvKeys($resourceId);
        $serializer = $this->getSerializer();
        $kvPair = array();
        foreach ($keys as $keyType => $key) {
            switch ($keyType) {
                case 'serialized':
                    $kvPair[$key] = $serializer->serialize($resource->getData());
                    break;
                case 'properties':
                    $kvPair[$key] = $serializer->serialize($resource->getProperties());
                    break;
                case 'string':
                    $kvPair[$key] = $resource->toString();
                    break;
                case 'type':
                    $kvPair[$key] = (string) $resource->getType();
                    break;
                case 'lastUpdated':
                    $properties = $resource->getProperties();
                    if (isset($properties['lastUpdated'])) {
                        $kvPair[$key] = (string) $properties['lastUpdated'];
                    } else {
                        $kvPair[$key] = time();
                    }
            }
        }
        return $kvPair;
    }

    public function kvPairsToAssocs(array $kvPairs) 
    {
        $serializer = $this->getSerializer();
        $res = array();
        asort($kvPairs);
        foreach ($kvPairs as $key => $value) {
            $match = array();
            preg_match($this->keyPattern, $key, $match);
            if (count($match) < 3) {
                continue;
            }
            $type = $match[1];
            if (!isset($res[$type])) {
                $res[$type] = array(); 
            }
            $innerId = $match[2];
            if (!isset($res[$type][$innerId])) {
                $res[$type][$innerId] = array();
                $res[$type][$innerId][$this->resourceTypeShortName] = $type;
                $res[$type][$innerId]['inner_id'] = $innerId;
                $res[$type][$innerId]['resource_id'] = $type . $this->resourceIdDelimiter . $innerId;
            }

            switch (count($match)) {
                case 3:
                    //'data'は,ResourceConfigで使われるキー名。
                    $res[$type][$innerId]['data'] = $serializer->unserialize($value);
                    break;
                case 4:
                    //'properties'は、ResourceConfigで使われるキー名。
                    switch ($match[3]) {
                        case 'props':
                            $res[$type][$innerId]['properties'] = $serializer->unserialize($value);
                            break;
                        case 'time':
                            $res[$type][$innerId]['lastUpdated'] = (int) $value;
                            break;
                        case 'str':
                            $res[$type][$innerId]['string'] = (string) $value;
                            break;
                    }
                    
                    break;
            }
        }
        return $res;
    }
    
    public function getPropDelimiter()
    {
        return $this->propDelimiter;
    }
    
    public function getSerializer()
    {
        if (!isset($this->serializer)) {
            $this->serializer = new Serializer;
        }
        return $this->serializer;
    }
}
