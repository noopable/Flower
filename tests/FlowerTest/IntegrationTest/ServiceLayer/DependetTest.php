<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\IntegrationTest\ServiceLayer;

use Zend\Di\Di;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Di\DiServiceInitializer;
/**
 * 依存ライブラリの機能確認
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class DependetTest extends \PHPUnit_Framework_TestCase {

    protected $di;
    protected $object;

    public function setUp()
    {
        $this->serviceLocator = new ServiceManager;
        $this->di = new Di;
        $instanceManager = $this->di->instanceManager();
        $instanceManager->addTypePreference('Zend\ServiceManager\ServiceLocatorInterface', $this->serviceLocator);
        $this->object = new DiServiceInitializer($this->di, $this->serviceLocator);
    }

    public function tearDown()
    {

    }

    public function testInitialize()
    {
        $mock = $this->getMock('Zend\ServiceManager\ServiceLocatorAwareInterface');
        $mock->expects($this->once())
                ->method('setServiceLocator')
                ->with($this->isInstanceOf('Zend\ServiceManager\ServiceManager'));
        $this->object->initialize($mock, $this->serviceLocator);

    }
}
