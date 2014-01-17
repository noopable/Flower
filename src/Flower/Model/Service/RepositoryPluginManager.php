<?php
namespace Flower\Model\Service;

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\ServiceManager\AbstractPluginManager;

use Flower\Model\RepositoryInterface;
use Flower\Model\Exception\RuntimeException;
/**
 * Description of RepositoryPluginManager
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RepositoryPluginManager extends AbstractPluginManager 
    implements PluginNamespaceInterface
{
    
    /**
     * クラスを配置する namespace as prefix
     * 他の場所のクラスを使いたいときは、直接getで取得するか、
     * 同じnamespaceにプロキシを配置する。
     * 
     * @var string 
     */
    protected $pluginNameSpace;
    
    /**
     * Repositoryは必須オプションをDiで設定してもらう必要がある。
     *
     * @var bool
     */
    protected $autoAddInvokableClass = false;
    
    /**
     * ServiceLocatorなどの取得にグローバルServiceLocatorをpeeringして使う
     * 
     * @var bool
     */
    protected $retrieveFromPeeringManagerFirst = false;
    
    public function setPluginNameSpace($pluginNameSpace)
    {
        $this->pluginNameSpace = (string) $pluginNameSpace;
    }
    
    public function getPluginNameSpace()
    {
        return $this->pluginNameSpace;
    }
    /**
     * Retrieve a service from the manager by name
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param  string $name
     * @param  array $options
     * @param  bool $usePeeringServiceManagers
     * @return object
     */
    public function byName($name, $options = array(), $usePeeringServiceManagers = true)
    {
        if (($pluginNameSpace = $this->getPluginNameSpace()) && (strpos($pluginNameSpace, $name) !== 0)) {
            $name = $pluginNameSpace . '\\' . $name;
        }
        
        return $this->get($name, $options, $usePeeringServiceManagers);
    }
    
    /**
     * 
     * @param \Flower\Model\RepositoryInterface $plugin
     * @return type
     * @throws RuntimeException
     */
    public function validatePlugin($plugin) {
        if ($plugin instanceof RepositoryInterface) {
            // we're okay
            return;
        }
        throw new RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Flower\Model\RepositoryInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
        /**
     * Retrieve a service from the manager by name
     *
     * Allows passing an array of options to use when creating the instance.
     * createFromInvokable() will use these and pass them to the instance
     * constructor if not null and a non-empty array.
     *
     * @param  string $name
     * @param  array $options
     * @param  bool $usePeeringServiceManagers
     * @return object
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        try {
            //※DiAbstractFactory経由でインスタンスを取ろうとする場合、
            //名前ベースになるので、$optionsは実質的に意味がなくなる。
            //initializerでconfigureする際には使える。
            $repository = parent::get($name, $options, $usePeeringServiceManagers);
        }
        catch (\Zend\ServiceManager\Exception\ServiceNotCreatedException $e) {
            $eTemp = $e;
            do {
                switch (true) {
                    case $eTemp instanceof \Zend\Di\Exception\MissingPropertyException:
                        trigger_error('Missing parameter for repository(' . $name . ') check configuration. (' . $eTemp->getMessage() . ')', E_USER_ERROR);
                        break;
                    case $eTemp instanceof \Zend\ServiceManager\Exception\ServiceNotCreatedException:
                        trigger_error($eTemp->getMessage(), E_USER_WARNING);
                        break;
                    default:
                        throw $eTemp;
                }
            } while($eTemp = $eTemp->getPrevious());
        }
        
        return $repository;
    }
}
