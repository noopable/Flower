<?php
return array(
    'classes' => array('container'),
    'tag' => 'body',//ルートレベルのタグは必要 テストでXMLエラーになる
    'inner' => array(
        array(
            'id' => 'overview',
            'order' => 100,
            'classes' => array('container', 'subhead', 'header'),
            'var' => 'header',
        ),
        array(
            'classes' => array('container',),
            'inner' => array(
                array(
                    'classes' => array('article', 'row', 'pull-right'),
                    //omitted var = content
                ),
                array(
                    'id' => 'sidebar',
                    'classes' => array('sidebar'),
                    'attributes' => ['style' => 'float:right;'],
                    'inner' => array(
                        'classes' => array('span3'),
                        'var' => 'sidebar',
                    ),

                ),
            ),
        ),
        array(
            'id' => 'footer',
            'inner' => array(
                'classes' => 'container',
                'var' => 'footer',
            ),
        ),
    ),
);