<?php
return array(
    'classes' => array('container'),
    'tag' => 'div',
    'inner' => array(
        array(
            'id' => 'overview',
            'order' => 100,
            'classes' => array('container','jumbotron', 'subhead', 'header'),
            //'inner' => array(
                //array(
                //    'classes' => 'container',
                    'var' => 'header',
               //),
            //)
        ),
        array(
            'classes' => array('container'),
            'inner' => array(
                array(
                    'classes' => array('article', 'row', 'pull-right'),
                    'inner' => array(
                        array(
                            'size' => 9,
                            'inner' => array(
                                array(
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
                    'classes' => array('sidebar', 'row'),
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