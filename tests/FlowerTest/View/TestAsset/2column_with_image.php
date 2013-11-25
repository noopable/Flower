<?php
return array(
    'classes' => array('container'),
    'tag' => '',//ルートレベルのタグをキャンセル
    'inner' => array(
        array(
            'id' => 'overview',
            'order' => 100,
            'classes' => array('container','jumbotron', 'subhead', 'header'/*'cBoth', 'row'*/),
            //'inner' => array(
                //array(
                //    'classes' => 'container',
                    'var' => 'header',
               //),
            //)
        ),
        array(
            'classes' => array('container', /*'tCal'*/),
            'inner' => array(
                array(
                    'classes' => array('article', 'row', 'pull-right'),
                    'inner' => array(
                        array(
                            'size' => 9,
                            'inner' => array(
                                array(
                                    //'size' => 9,
                                    'var' => 'categoryImage',
                                ),
                                array(
                                    'classes' => 'row',
                                    'inner' => array(
                                        'size' => 9,
                                        'var' => 'content',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'id' => 'sidebar',
                    'classes' => array('sidebar', 'row'/*, 'span3'*/),
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