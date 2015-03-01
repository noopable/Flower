<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\IntegrationTest\AccessControl;

use Flower\AccessControl\AccessControlServiceFactory;
use Flower\AccessControl\RoleMapper\RoleMapperInterface;
use Flower\Person\Identical\Email;
use Flower\Person\EmailRepository;
use Flower\Test\TestTool;
use FlowerTest\IntegrationTest\TestAsset\ServiceLocator;
use Zend\Db\TableGateway\TableGateway;

/**
 * 認証情報の追加と認証情報の確認を行うテスト
 *
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class AuthenticateTest extends \PHPUnit_Framework_TestCase
{


    protected $serviceLocator;

    protected $ACService;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $config = include __DIR__ . '/TestAsset/standard.config.php';
        $this->serviceLocator = ServiceLocator::getServiceLocator($config);
        $this->ACService = $this->serviceLocator->get('FlowerTest_AccessControl');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     *
     */
    public function testAuthenticate()
    {
        if (!extension_loaded('pdo_mysql')) {
            $this->markTestSkipped(
              'Pdo_MySQL 拡張モジュールが使用できません。'
            );
        }
        $mailAddress = str_replace('\\', '_', __CLASS__) . '@example.com';
        $password = uniqid();
        $config = $this->serviceLocator->get('Config');
        $accessControlService = $this->ACService;
        $driverInfo = $config['test_auth_db']['driver'];
        $adapter = new \Zend\Db\Adapter\Adapter($driverInfo);
        $connection = $adapter->getDriver()->getConnection();
        $connection->connect();

        $emailTable = new TableGateway($config['test_auth_db']['table_name'], $adapter);
        $emailRepository = new EmailRepository('email_test', new Email, $emailTable);
        $emailRepository->setOption('columns',
            array(
                'email' => 'email',
                'name' => 'name',
                'person_id' => 'primary_person_id',//person_idの変換が効いていない？
                'credential' => 'credential',
                'roles' => 'roles',
                'activation_code' => 'activation_code', //末尾にtimeを入れて、期限切れチェック、改行で3セットまで可
                'status' => 'status',//disable wait active
                'lastupdated' => 'lastupdated',
            )
        );
        $emailRepository->initialize();
        $email = $emailRepository->getEntity(array('email' => $mailAddress));
        if (false === $email) {
            $email = $emailRepository->create();
            $email->setIdentity($mailAddress);
        }
        $email->roles = 'admin,visitor';
        $email->password = $password;

        $emailRepository->save($email);

        $authAdapter = $accessControlService->getAuthService()->getAdapter();
        $credentialValidationCallback = TestTool::getPropertyValue($authAdapter, 'credentialValidationCallback');
        $this->assertTrue($credentialValidationCallback(\Flower\Hash\Hash1::hash($password), $password));
        $res = $accessControlService->authenticate($mailAddress, $password);
        if (!$res) {
            $authResult = $accessControlService->getAuthResult();
            switch ($authResult->getCode()) {
                case -1:
                    $this->assertTrue($res, 'FAILURE_IDENTITY_NOT_FOUND' . PHP_EOL . var_export($authResult->getMessages(), true));
                    break;
                case -2:
                    $this->assertTrue($res, 'FAILURE_IDENTITY_AMBIGUOUS' . PHP_EOL . var_export($authResult->getMessages(), true));
                    break;
                case -3:
                    $this->assertTrue($res, 'FAILURE_CREDENTIAL_INVALID' . PHP_EOL . var_export($authResult->getMessages(), true));
                    break;
                case -4:
                    $this->assertTrue($res, 'FAILURE_UNCATEGORIZED' . PHP_EOL . var_export($authResult->getMessages(), true));
                    break;
            }
        }
        $role = $accessControlService->getRole();
        $this->assertEquals(RoleMapperInterface::BUILT_IN_CURRENT_CLIENT_AGGREGATE, $role);
        $this->assertContains('admin', $role->getParents());
    }
}
