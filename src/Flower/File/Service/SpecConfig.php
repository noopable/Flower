<?php
namespace Flower\File\Service;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\File\Spec\SpecInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 *
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
Class SpecConfig
{
    /**
     *
     * @var array
     */
    protected $config;

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    protected $defaultSpec = 'Flower\File\Spec\TreeArrayMerge';

    protected $defaultGateway = 'Flower\File\Gateway\Gateway';

    protected $defaultResolveSpec = 'Flower\File\Spec\Resolver\Tree';

    protected $defaultCacheSpec = 'Flower\File\Spec\Cache\DirectoryCacheSpec';

    protected $defaultMergeSpec = 'Flower\File\Spec\Merge\ArrayMerge';

    protected $defaultFileAdapter = 'Flower\File\Adapter\ZendConfig';

    public function __construct(array $config)
    {
        $this->config = $config;
        if (isset($config['serviceLocator'])) {
            $this->serviceLocator = $config['serviceLocator'];
        }
    }

    public function configure(SpecInterface $spec)
    {
        if (!$spec->getGateway()) {
            $spec->setGateway($this->createGateway());
        }

        if (!$spec->getResolveSpec()) {
            $spec->setResolveSpec($this->createResolveSpec());
        }

        if (!$spec->getCacheSpec()) {
            $spec->setCacheSpec($this->createCacheSpec());
        }

        if (!$spec->getMergeSpec()) {
            $spec->setMergeSpec($this->createMergeSpec());
        }

        if (!$spec->getFileAdapter()) {
            $spec->setFileAdapter($this->createFileAdapter());
        }

        return $spec;
    }

    public function createSpec()
    {
        if (isset($this->config['spec_class'])) {
            $class = $this->config['spec_class'];
        }
        else {
            $class = $this->defaultSpec;
        }

        $options = null;
        if (isset($this->config['spec_options'])) {
            $options = $this->config['spec_options'];
        }

        //don't call configure
        return $this->createWellKnownInstance($class, $options, __FUNCTION__);
    }

    public function createGateway()
    {
        if (isset($this->config['gateway_class'])) {
            $class = $this->config['gateway_class'];
        }
        else {
            $class = $this->defaultGateway;
        }

        $options = null;
        if (isset($this->config['gateway_options'])) {
            $options = $this->config['gateway_options'];
        }

        $instance = $this->createWellKnownInstance($class, $options, __FUNCTION__);
        if (method_exists($instance, 'configure')) {
            $instance->configure();
        }
        return $instance;

    }

    public function createResolveSpec()
    {
        if (isset($this->config['resolve_spec_class'])) {
            $class = $this->config['resolve_spec_class'];
        }
        else {
            $class = $this->defaultResolveSpec;
        }

        $options = null;
        if (isset($this->config['resolve_spec_options'])) {
            $options = $this->config['resolve_spec_options'];
        }

        $instance = $this->createWellKnownInstance($class, $options, __FUNCTION__);
        if (method_exists($instance, 'configure')) {
            $instance->configure();
        }
        return $instance;
    }

    public function createCacheSpec()
    {
        if (isset($this->config['cache_spec_class'])) {
            $class = $this->config['cache_spec_class'];
        }
        else {
            $class = $this->defaultCacheSpec;
        }

        $options = null;
        if (isset($this->config['cache_spec_options'])) {
            $options = $this->config['cache_spec_options'];
        }

        $instance = $this->createWellKnownInstance($class, $options, __FUNCTION__);
        if (method_exists($instance, 'configure')) {
            $instance->configure();
        }
        return $instance;
    }

    public function createMergeSpec()
    {
        if (isset($this->config['merge_spec_class'])) {
            $class = $this->config['merge_spec_class'];
        }
        else {
            $class = $this->defaultMergeSpec;
        }

        $options = null;
        if (isset($this->config['merge_spec_options'])) {
            $options = $this->config['merge_spec_options'];
        }

        $instance = $this->createWellKnownInstance($class, $options, __FUNCTION__);
        if (method_exists($instance, 'configure')) {
            $instance->configure();
        }
        return $instance;
    }

    public function createFileAdapter()
    {
        if (isset($this->config['file_adapter_class'])) {
            $class = $this->config['file_adapter_class'];
        }
        else {
            $class = $this->defaultFileAdapter;
        }

        $options = null;
        if (isset($this->config['file_adapter_options'])) {
            $options = $this->config['file_adapter_options'];
        }

        $instance = $this->createWellKnownInstance($class, $options, __FUNCTION__);
        if (method_exists($instance, 'configure')) {
            $instance->configure();
        }
        return $instance;

    }

    /**
     * create instance that has common convention
     *
     * @param type $class
     * @param type $options
     * @param type $type
     * @return type
     * @throws \RuntimeException
     */
    public function createWellKnownInstance($class, $options = null, $type = '')
    {
        if (! class_exists($class)) {
            throw new \RuntimeException($type . ' configuration error \''
                            . $class . '\' is not exists' );
        }
        /*
        $reflection = new \ReflectionClass($class);
        if (! $reflection->isInstantiable()) {
            throw new \RuntimeException($type . ' configuration error \''
                            . $class . '\' is not instantiable' );
        }
         */
        $instance = new $class($options);

        if (isset($this->serviceLocator) && $instance instanceof ServiceLocatorAwareInterface) {
            $instance->setServiceLocator($this->serviceLocator);
        }

        return $instance;
    }
}
