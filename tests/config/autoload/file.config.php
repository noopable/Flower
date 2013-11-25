<?php
return array(
    'test_flower_file' => Array (
        'spec_class' => 'Flower\File\Spec\TreeArrayMerge',
        'spec_options' => Array (),
        'resolve_spec_class' => 'Flower\File\Spec\Resolver\Tree',
        'resolve_spec_options' => array(
            'map' => Array (),
            'path_stack' => Array (
                'flower' => __DIR__ . '/../../FlowerTest/File/TestAsset/data',
            )
        ),
        'cache_spec_options' => array(
            'cache_path' => __DIR__ . '/../../FlowerTest/File/TestAsset/data/cache',
            //'cache_path' => 'vfs://cache',
        ),
    )
);