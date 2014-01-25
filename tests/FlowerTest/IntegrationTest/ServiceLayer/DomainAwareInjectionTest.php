<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace FlowerTest\IntegrationTest\ServiceLayer;

use Flower\Domain\Service;
use Flower\Domain\CurrentDomain;
use FlowerTest\Domain\TestAsset\ConcreteDomainAware;
use Zend\Di\Di;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Di\DiServiceInitializer;
/**
 * DomainAwareInterfaceを実装したサービスレイヤーが、
 * DiServiceInitializerからCurrentDomainを受け取ることができるか確認
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class DomainAwareInjectionTest extends \PHPUnit_Framework_TestCase {

    protected $di;

    protected $serviceLocator;

    protected $object;

    protected $currentDomain;

    protected $concreteDomainAware;

    public function setUp()
    {
        $this->concreteDomainAware = new ConcreteDomainAware;
        $domainService = new Service;
        $this->currentDomain = new CurrentDomain($domainService);
        $this->serviceLocator = new ServiceManager;
        $this->di = new Di;
        $instanceManager = $this->di->instanceManager();
        $instanceManager->addTypePreference('Flower\Domain\DomainInterface', $this->currentDomain);
        $this->object = new DiServiceInitializer($this->di, $this->serviceLocator);
    }

    public function tearDown()
    {

    }

    public function testInitialize()
    {
        $this->object->initialize($this->concreteDomainAware, $this->serviceLocator);
        $this->assertSame($this->currentDomain, $this->concreteDomainAware->getDomain());
    }
}
