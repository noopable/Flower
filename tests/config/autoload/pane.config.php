<?php
return array(
    'flower_pane' => Array (
        'builder_options' => array(
            'size_to_class_function' => function($size) { 
                $sizes = ['foo', 'bar', 'baz'];
                return $sizes[$size];
            },
        ),
    )
);