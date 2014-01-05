<?php
namespace Flower\Imagine\Filter;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Imagine\Image\ImageInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use RuntimeException;
/**
 * Description of FilterFactory
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class FilterFactory implements FactoryInterface
{
    protected $configKey = 'fl_imagine';

    protected $defaultConfig = array(
        'engine' => 'gd',
        'strategy' => array(
            'default-mode' => ImageInterface::THUMBNAIL_INSET,
            'max-size' => 786432,// 1024 * 768
        ),
        'thumbnails' => array(
            'max' => array(1024, 768),
            'preview' => array(400, 400),
            //'mini' =>  array(120, 1, ImageInterface::THUMBNAIL_OUTBOUND),
        ),
    );

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        if ($serviceLocator->has('Config')) {
            $globalConfig = $serviceLocator->get('Config');
            if (isset($globalConfig[$this->configKey])) {
                //notice! don't merge recursive
                $config = array_merge($this->defaultConfig, $globalConfig[$this->configKey]);
            } else {
                $config = $this->defaultConfig;
            }
        }

        $service = new ImagineThumbnail;

        switch (strtolower($config['engine'])) {
            case 'gd':
                if (!function_exists('gd_info')) {
                    throw new RuntimeException('Gd not installed');
                }
                $class = 'Imagine\Gd\Imagine';
                break;
            case 'imagick':
                if (!class_exists('Imagick')) {
                    throw new RuntimeException('Imagick not installed');
                }
                $class = 'Imagine\Imagick\Imagine';
                break;
            case 'gmagick':
                if (!class_exists('Gmagick')) {
                    throw new RuntimeException('Gmagick not installed');
                }
                $class = 'Imagine\Gmagick\Imagine';
                break;
        }
        $imagine = new $class;
        $service->setImagine($imagine);
        $service->setThumbnailStrategy($config['strategy']);
        $service->setThumbnails($config['thumbnails']);

        return $service;

    }
}
