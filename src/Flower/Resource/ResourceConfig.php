<?php
namespace Flower\Resource;

/*
 * Here comes the text of your license
 * Each line should be prefixed with  *
 */
use Flower\Resource\ResourceClass\ResourceInterface as NewResourceInterface;
use Flower\Resource\ResourceClass\WakeUpInterface;
/**
 * Description of ResourceConfig
 *
 * @author tomoaki
 */
class ResourceConfig {
    protected $config;

    public function __construct(array $config = null)
    {
        if (null !== $config) {
            $this->config = $config;
        }
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function configure(NewResourceInterface $resource)
    {
        $config = $this->config;
        if (isset($config['data'])) {
            $resource->setData($config['data']);
            unset($config['data']);
        }

        if (isset($config['properties'])) {
            $resource->setProperties($config['properties']);
            unset($config['properties']);
        }

        if (isset($config['type'])) {
            $resource->setType($config['type']);
            unset($config['type']);
        }

        if (isset($config['inner_id'])) {
            $resource->setInnerId($config['inner_id']);
            unset($config['inner_id']);
        }

        if (isset($config['resource_id'])) {
            $resource->setResourceId($config['resource_id']);
            unset($config['resource_id']);
        }

        if (count($config) > 0) {
            $resource->setOptions($config);
        }

        if ($resource instanceof WakeUpInterface) {
            $resource->wakeup();
        }
    }
}
