<?php
return array(
    'test_flower_resource_manager' => array(
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
                    'namespace' => 'flower_resource_manager',
                    'cache_dir' => dirname(dirname(__DIR__)) . '/tmp/resource',
                    'dir_level' => 2,
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => true),
            ),
        ),
    ),
);