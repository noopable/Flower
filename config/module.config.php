<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Flower_File_Adapter' => 'Flower\File\Service\FileServiceFactoryFromConfig',
            'Flower\FormPostRedirectGet\Plugin\FilePostRedirectGet' => 'Flower\FilePostRedirectGet\Service\Factory',
            'Flower_ImagineFilter' => 'Flower\Imagine\Filter\FilterFactory',
            'Flower_Resource_Manager'
                => 'Flower\Resource\ResourceManagerFactory',
            'Flower_Resources'
                => 'Flower\Resource\ResourcePluginManagerFactory',
            'Flower_LazyRouteLoaderFile' => 'Flower\Router\LazyRouteLoaderFileFactory',
            'Flower_LazyRouteLoaderResource' => 'Flower\Router\LazyRouteLoaderResourceFactory'
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'FlowerFileSender' => 'Flower\FilePostRedirectGet\Controller\FileSendController',
        ),
    ),
    'di' => array(
        'instance' => array(
            'Flower\File\Spec\TreeArrayMerge' => array(
                'parameters' => array(

                ),
            ),
        ),
    ),
    'router' => array(
        //override Zend\Mvc\Router\Http\TreeRouteStack
        'router_class' => 'Flower\Router\TreeRouteStack',
    ),
    'flower_lazy_load_route_file' => array(
        'spec_class' => 'Flower\File\Spec\TreeArrayMerge',
        'spec_options' => array(
        ),
        'resolve_spec_class' => 'Flower\File\Spec\Resolver\Tree',
        'resolve_spec_options' => array(
            'map' => [],
            'path_stack' => array(
                'flower' => __DIR__ . '/../data/routes',
            ),
        ),
        'cache_spec_options' => array(
            'cache_path' => __DIR__ . '/../data/routes/cache',
        ),
    ),
    'flower_lazy_load_route_resource' => array(
        'resource_plugin_manager' => 'Flower_Resources',
        //reserve configuration
        /**
         * class => マネージャークラスを換装できる
         * その他はResource\Manager\Config経由でconfigureされるオプション
         *
         */
        //@see http://framework.zend.com/manual/2.2/en/modules/zend.cache.storage.adapter.html
        'cache_storage' => array(
            'adapter' => array(
                'name'    => 'filesystem',
                'options' => array(
                    'namespace' => 'flower_lazy_load_route',
                    'cache_dir' => dirname(__DIR__) . '/data/resource',
                    'dir_level' => 2,
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => true),
            ),
        ),
    ),
    'flower_file' => array(
        'spec_class' => 'Flower\File\Spec\TreeArrayMerge',
        'spec_options' => array(
        ),
        'resolve_spec_class' => 'Flower\File\Spec\Resolver\Tree',
        'resolve_spec_options' => array(
            'map' => [],
            'path_stack' => array(
                'flower' => __DIR__ . '/../data/file',
            ),
        ),
        'cache_spec_options' => array(
            'cache_path' => __DIR__ . '/../data/cache/file',
        ),
    ),
    'flower_resource_manager' => array(
        'resource_plugin_manager' => 'Flower_Resources',
        //reserve configuration
        /**
         * class => マネージャークラスを換装できる
         * その他はResource\Manager\Config経由でconfigureされるオプション
         *
         */
        //@see http://framework.zend.com/manual/2.2/en/modules/zend.cache.storage.adapter.html
        'cache_storage' => array(
            'adapter' => array(
                'name'    => 'filesystem',
                'options' => array(
                    'namespace' => 'zfcache_resource_manager',
                    'cache_dir' => dirname(__DIR__) . '/data/resource',
                    'dir_level' => 2,
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => true),
            ),
        ),
    ),
    'flower_resources' => array(
        'invokables' => array(
            'generic' => 'Flower\Resource\ResourceClass\Resource',
        ),
    ),
    'controller_plugins' => array(
        'factories' => array(
            'fprg' => 'Flower\FilePostRedirectGet\Service\PluginFactory',
        ),
    ),
    'fprg' => array(
        'tmp-dir' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'fprg',
        'thumbnail-filter' => 'Flower_ImagineFilter',
    ),
    'view_helpers' => array(
        'factories' => array(
            'pane' => 'Flower\View\Pane\HelperFactory',
        ),
        'invokables' => array(
            //'pane' => 'Flower\View\Pane\PaneHelper',
            'npMenu' => 'Flower\View\Navigation\NpMenu',
            'formPreview' => 'Flower\Form\View\FormPreview',
            'form-element' => 'Flower\Form\View\FormElement',
            'form-file' => 'Flower\FilePostRedirectGet\View\FormFile',
            'bodyScript' => 'Flower\View\BodyScript',
        ),
    ),
);
