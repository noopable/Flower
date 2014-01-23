<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */


return array(
    'service_manager' => array(
        'factories' => array(
            //module.config.phpに記載があるので重複する必要はない
            //'Flower_Resources' => 'Flower\Resource\ResourcePluginManagerFactory',
            //'Flower_LazyRouteLoaderFile' => 'Flower\Router\LazyRouteLoaderFileFactory',
            //'Flower_LazyRouteLoaderResource' => 'Flower\Router\LazyRouteLoaderResourceFactory'
        ),
    ),
    'router' => array(
        //override Zend\Mvc\Router\Http\TreeRouteStack
        'router_class' => 'Flower\Router\TreeRouteStack',
        'routes' => array(
            'first' => array(
                'type' => 'literal',
                'may_terminate' => true,
                'options' => array(
                    'route' => '/abc',
                    'defaults' => array(
                        'bao' => 'bar',
                    ),
                ),
            ),
            'second' => array(
                'type' => 'literal',
                'options' => array(
                    'route' => '/foo',
                    'defaults' => array(
                        'bar' => 'baz',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'file' => array(
                        'type' => 'lazyfile',
                        'may_terminate' => true,
                        'options' => array(
                            'name' => 'foo',
                        ),
                    ),
                ),
            ),

        ),
    ),
    'flower_lazy_load_route_file' => array(
        'spec_class' => 'Flower\File\Spec\TreeArrayMerge',
        'spec_options' => array(
        ),
        'resolve_spec_class' => 'Flower\File\Spec\Resolver\Tree',
        'resolve_spec_options' => array(
            'map' => [],
            'path_stack' => array(
                'flower' => __DIR__ . '/tmp/routes',
            ),
        ),
        'cache_spec_options' => array(
            'cache_path' => __DIR__ . '/tmp/routes/cache',
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
                    'cache_dir' => __DIR__ . '/tmp/resource',
                    'dir_level' => 2,
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => true),
            ),
        ),
    ),
);