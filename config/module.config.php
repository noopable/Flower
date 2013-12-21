<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'Flower_File_Adapter' => 'Flower\File\Service\FileServiceFactoryFromConfig',
            'Flower\FormPostRedirectGet\Plugin\FilePostRedirectGet' => 'Flower\FilePostRedirectGet\Service\Factory',
            'Flower_ImagineFilter' => 'Flower\Imagine\Filter\FilterFactory',
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
        ),
    ),
);
