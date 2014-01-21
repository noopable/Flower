<?php
$accessControlServiceName = 'FlowerTest_AccessControl';
$authServiceName = 'FlowerTest_AuthenticationService';
$authDbAdapterName = 'FlowerTest_Auth_DbAdapter';
$resourceManagerServiceName = 'FlowerTest_Resource_Manager';
$resourcePluginManagerServiceName = 'FlowerTest_Resources';
return array(
    'service_manager' => array(
        'factories' => array(
            $accessControlServiceName => 'Flower\AccessControl\AccessControlServiceFactory',
            $resourceManagerServiceName
                => 'Flower\Resource\ResourceManagerFactory',
            $resourcePluginManagerServiceName
                => 'Flower\Resource\ResourcePluginManagerFactory',
            $authServiceName => function($sm, $cName, $rName) use($authDbAdapterName) {
                return new \Zend\Authentication\AuthenticationService(
                    null, //Session Storage or ResourceStorage or Chain etc.
                    $sm->get($authDbAdapterName)
                );
            },
            $authDbAdapterName => function($sm, $cName, $rName) {
                $db = $sm->get('Config')['test_auth_db'];
                $dbAdapter = new \Zend\Db\Adapter\Adapter($db['driver']);
                $authAdapter = new \Zend\Authentication\Adapter\DbTable\CredentialTreatmentAdapter(
                            $dbAdapter, //DbAdapter $zendDb,
                            $db['table_name'],//$tableName = null,
                            $db['identity_column'],//$identityColumn = null,
                            $db['credential_column'],//$credentialColumn = null,
                            $db['credential_treatment']//$credentialTreatment = null
                        );
                return $authAdapter;
            }
        ),
    ),
    //driverは autoload/auth.local.php等から取得した方がよい。
    //これは簡易テストのために同じファイルに記述
    //@todo データベース接続情報をgitから
    //@todo CIテスト用にsqliteをつかってもよい。
    'test_auth_db' => array(
        'driver' => include(__DIR__ . '/sandbox.db.php'),
        'table_name' => 'email',
        'identity_column' => 'email',
        'credential_column' => 'password',
        //sha1は既に安全ではありません。
        'credential_treatment' => 'SHA1(?)',
    ),
    'flower_access_control' => array(
        'auth_service' => $authServiceName,
        'resource_manager' => $resourceManagerServiceName,
        'resource_plugin_manager' => $resourcePluginManagerServiceName,
        'acl_path' => __DIR__ . '/acl.php',
        'auth_result_omit_columns' => array('password'),
    ),
    'flower_resource_manager' => array(
        'resource_plugin_manager' => $resourcePluginManagerServiceName,
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
                    'namespace' => 'flower_test_resource_manager',
                    'cache_dir' => FlowerTest\Bootstrap::getTestRootDir() . '/tmp/resource',
                    'dir_level' => 2,
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => true),
            ),
        ),
    ),
);
