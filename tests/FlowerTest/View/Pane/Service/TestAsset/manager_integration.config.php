<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */


return array(
    'service_manager' => array(
        'factories' => array(
            'Test_FileListener' => 'Flower\View\Pane\Service\ConfigFileListenerFactory',
            'Test_CacheListener' => 'Flower\View\Pane\Service\CacheListenerFactory',
        ),
    ),
    'view_helpers' => array(
        'factories' => array(
            'npPaneManager' => 'Flower\View\Pane\Service\ManagerFactory',
        ),
    ),
    'flower_pane_manager' => array(
        'pane_config' => array(
            'bar' => array(
                'pane_class' => 'Flower\View\Pane\PaneClass\Anchor',
                'classes' => 'container',
                'var' => 'Link Label 1',// this will be omitted
                'inner' => array(
                    'classes' => 'main',
                    'var' => 'Link Label 1.1',
                    'inner' => array(
                        'classes' => 'main',
                        'var' => 'Link Label 1.1.1',
                    ),
                ),
            ),
        ),
        'builder_options' => array(
            'builder_class' => 'Flower\View\Pane\Builder\Builder',
            'pane_class' => 'Flower\View\Pane\PaneClass\Pane',
        ),
        'renderer_class' => 'Flower\View\Pane\PaneRenderer',
        'listener_aggregates' => array(
            'Test_FileListener',
            'Test_CacheListener',
            //'FlowerTest\View\Pane\Service\TestAsset\MockListenerAggregate',
        ),
    ),
    'pane_config_file_listener' => array(
        'spec_class' => 'Flower\File\Spec\TreeArrayMerge',
        'spec_options' => array(
        ),
        'resolve_spec_class' => 'Flower\File\Spec\Resolver\Tree',
        'resolve_spec_options' => array(
            'map' => [],
            'path_stack' => array(
                'flower' => __DIR__ . '/File',
            ),
        ),
        'cache_spec_options' => array(
            'cache_path' => __DIR__ . '/tmp/cache',
        ),
    ),
    'pane_cache_listener' => array(
        //@see http://framework.zend.com/manual/2.2/en/modules/zend.cache.storage.adapter.html
        'cache_storage' => array(
            'adapter' => array(
                'name'    => 'filesystem',
                'options' => array(
                    'namespace' => 'test_pane_manager',
                    'cache_dir' => __DIR__ . '/tmp/cache',
                    'dir_level' => 1,
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => true),
            ),
        ),
    ),
);