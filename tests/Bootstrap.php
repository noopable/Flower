<?php
namespace FlowerTest;
/**
 * This file is copy from zf2/tutorial/album and minor modify

 *  */
use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

Bootstrap::init();
class Bootstrap
{
    protected static $serviceManager;

    public static function init()
    {
        // Load the user-defined test configuration file, if it exists; otherwise, load
        if (is_readable(__DIR__ . '/TestConfig.php')) {
            $testConfig = include __DIR__ . '/TestConfig.php';
        } else {
            $testConfig = include __DIR__ . '/TestConfig.php.dist';
        }

        if (isset($testConfig['module_listener_options'])
                && isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePathsConfig = $testConfig['module_listener_options']['module_paths'];
        }
        else {
            $modulePathsConfig = array('vendor', 'module');
        }

        $zf2ModulePaths = array(dirname(dirname(__DIR__)));
        foreach ($modulePathsConfig as $moduleDirName) {
            if (($path = static::findParentPath($moduleDirName))) {
                $zf2ModulePaths[] = $path;
            }
        }

        $zf2ModulePaths  = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ?: (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        $ref = new \ReflectionClass('Zend\Console\Console');
        $prop = $ref->getProperty('isConsole');
        $prop->setAccessible(true);
        $prop->setValue(false);

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = array(
            'module_listener_options' => array(
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ),
        );

        $config = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        //$serviceManager->get('Application')->bootstrap();
        static::$serviceManager = $serviceManager;

        /**
         * 各テストで副作用が考えらるときは、static::$serviceManagerを使わず、Configだけ
         * 移して個別に作成すること
        $config = Bootstrap::getServiceManager()->get('ApplicationConfig');
        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        $serviceManager->get('Application')->bootstrap();
         *
         */
    }

    public static function getServiceManager()
    {
        return static::$serviceManager;
    }

    protected static function initAutoloader()
    {
        $vendorPath = static::findParentPath('vendor');

        if (false === $vendorPath) {
            // */noopable/__NAMESPACE__/tests
            $vendorPath = dirname(dirname(dirname(__DIR__)));
        }
        //we have composer autoloader?
        if (is_readable($vendorPath . '/autoload.php')) {
            $loader = require_once $vendorPath . '/autoload.php';
            error_log('composer loaded');
        } else {
            //for testing only but if this was installed with git submodule .hmm...
            require_once(realpath(dirname(__DIR__) . '/Module.php'));
        }

        if (! class_exists('Zend\Loader\StandardAutoloader')) {
            //try to load ZF2 autoload class
            if (! $zf2Path = getenv('ZF2_PATH')) {
                if (defined('ZF2_PATH')) {
                    $zf2Path = ZF2_PATH;
                }
                elseif(is_dir($vendorPath . '/ZF2/library')) {
                    $zf2Path = $vendorPath . '/ZF2/library';
                }
                elseif (is_dir($vendorPath . '/zendframework/zendframework/library')) {
                    $zf2Path = $vendorPath . '/zendframework/zendframework/library';
                }
            }

            if (!$zf2Path) {
                throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
            }

            if (isset($loader)) {
                $loader->add('Zend\\', $zf2Path . '/Zend');
            } else {
                require_once $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
            }

            if (! class_exists('Zend\Loader\AutoloaderFactory')) {
                throw new RuntimeException('faild to load zf2 AutoloaderFactory from ' . $zf2Path );
            }

        }

        AutoloaderFactory::factory(array(
            'Zend\Loader\StandardAutoloader' => array(
                'autoregister_zf' => true,
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                ),
            ),
        ));


    }

    protected static function findParentPath($path)
    {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) return false;
            $previousDir = $dir;
        }
        return realpath($dir . '/' . $path);
    }

    public static function getTestRootDir()
    {
        static $dir;
        if (! isset($dir)) {
            $dir = __DIR__;
        }
        return $dir;
    }
}


