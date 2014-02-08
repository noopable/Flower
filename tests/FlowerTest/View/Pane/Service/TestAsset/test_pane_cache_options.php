<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

return array(
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
);