<?php
namespace Flower\FilePostRedirectGet\Service;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\FilePostRedirectGet\Plugin\FilePostRedirectGet;

use Zend\Filter\FilterInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * Description of Factory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Factory implements FactoryInterface
{
    protected $configKey = 'fprg';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $fprg = new FilePostRedirectGet;

        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        if (! $serviceLocator->has('Config')) {
            return $fprg;
        }
        $config = $serviceLocator->get('Config');
        if (!isset($config[$this->configKey])) {
            return $fprg;
        }

        foreach ($config[$this->configKey] as $key => $value) {
            $key = strtolower($key);
            switch ($key) {
                case 'tmp-dir-policy':
                    $fprg->setTmpDirPolicy($value);
                    break;
                case 'tmp-dir':
                    $fprg->setTmpBaseDir($value);
                    break;
                case 'preview-route':
                    $fprg->setPreviewRoute($value);
                    break;
                case 'preview-params':
                    $fprg->setPreviewParams($value);
                    break;
                case 'thumbnail-filter':
                    //callableは受け入れなくていい？
                    //serviceLocatorじゃなくfilterPluginManagerを通すべき？
                    if (is_string($value)) {
                        if ($serviceLocator->has($value)) {
                            $value = $serviceLocator->get($value);
                        }
                    }
                    if ($value instanceof FilterInterface) {
                        $fprg->setThumbnailFilter($value);
                    }
                    break;
            }
        }

        return $fprg;
    }
}
