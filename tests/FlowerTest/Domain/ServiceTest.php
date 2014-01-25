<?php
namespace FlowerTest\Domain;

use Flower\Domain\Service;
use Flower\Test\TestTool;
use Zend\EventManager\EventManager;
use Zend\Di\Di;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\CallbackHandler;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-25 at 00:17:40.
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Service
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Service;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\Domain\Service::createCurrentDomain
     */
    public function testCreateCurrentDomain()
    {
        $res = $this->object->createCurrentDomain();
        $this->assertInstanceOf('Flower\Domain\CurrentDomain', $res);
    }

    public function testCreateCurrentDomainWithParams()
    {
        $domainId = 123;
        $domainName = 'example.com';
        $this->object->setCurrentDomainId($domainId);
        $this->object->setCurrentDomainName($domainName);
        $res = $this->object->createCurrentDomain();
        $this->assertEquals($domainId, $res->getDomainId());
        $this->assertEquals($domainName, $res->getDomainName());
    }

    /**
     * @covers Flower\Domain\Service::createDomain
     */
    public function testCreateDomain()
    {
        $domainId = 123;
        $domainName = 'example.com';
        $res = $this->object->createDomain($domainId, $domainName);
        $this->assertInstanceOf('Flower\Domain\Domain', $res);
        $this->assertEquals($domainId, $res->getDomainId());
        $this->assertEquals($domainName, $res->getDomainName());
    }

    /**
     * @covers Flower\Domain\Service::getCurrentDomain
     */
    public function testGetCurrentDomain()
    {
        $this->assertNull(TestTool::getPropertyValue($this->object, 'currentDomain'));
        $res = $this->object->getCurrentDomain();
        $this->assertInstanceOf('Flower\Domain\CurrentDomain', $res);
    }

    /**
     * @covers Flower\Domain\Service::setCurrentDomainName
     */
    public function testSetCurrentDomainName()
    {
        $domainName = 'example.com';
        $this->object->setCurrentDomainName($domainName);
        $this->assertEquals($domainName, TestTool::getPropertyValue($this->object, 'currentDomainName'));
    }

    /**
     * @covers Flower\Domain\Service::setCurrentDomainId
     */
    public function testSetCurrentDomainId()
    {
        $domainId = 123;
        $this->object->setCurrentDomainId($domainId);
        $this->assertEquals($domainId, TestTool::getPropertyValue($this->object, 'currentDomainId'));
    }

    /**
     * @covers Flower\Domain\Service::onBootstrap
     */
    public function testOnBootstrap()
    {
        $di = new Di;
        $serviceLocator = new ServiceManager;
        $serviceLocator->setService('Di', $di);
        $serviceLocator->setService('EventManager', $this->getMock('Zend\EventManager\EventManager'));
        $serviceLocator->setService('Request', $this->getMock('Zend\Http\Request'));
        $serviceLocator->setService('Response', $this->getMock('Zend\Stdlib\Response'));
        $application = new Application(array(), $serviceLocator);
        $mvcEvent = new MvcEvent;
        $mvcEvent->setApplication($application);
        $this->object->onBootstrap($mvcEvent);
        $instanceManager = $di->instanceManager();
        $this->assertTrue($instanceManager->hasSharedInstance('Flower\Domain\CurrentDomain'));
        $this->assertTrue($instanceManager->hasTypePreferences('Flower\Domain\DomainInterface'));
        $res = $di->get('Flower\Domain\CurrentDomain');
        $this->assertInstanceOf('Flower\Domain\CurrentDomain', $res);
    }

    /**
     * @covers Flower\Domain\Service::onRoute
     */
    public function testOnRoute()
    {
        $domainId = 123;
        $domainName = 'example.com';
        $routeMatch = new RouteMatch(array('domain_id' => $domainId, 'domain_name' => $domainName));
        $mvcEvent = new MvcEvent;
        $mvcEvent->setRouteMatch($routeMatch);
        $this->object->onRoute($mvcEvent);
        $this->assertEquals($domainId, TestTool::getPropertyValue($this->object, 'currentDomainId'));
        $this->assertEquals($domainName, TestTool::getPropertyValue($this->object, 'currentDomainName'));
        $currentDomain = $this->object->getCurrentDomain();
        $this->assertEquals($domainId, $currentDomain->getDomainId());
        $this->assertEquals($domainName, $currentDomain->getDomainName());
    }

    /**
     * @covers Flower\Domain\Service::attach
     */
    public function testAttach()
    {
        $eventManager = new EventManager;
        $eventManager->attachAggregate($this->object);

        $onBootstrapListners = $eventManager->getListeners(MvcEvent::EVENT_BOOTSTRAP);
        $this->assertCount(1, $onBootstrapListners);
        $listener1 = $onBootstrapListners->getIterator()->current();
        $this->assertInstanceOf('Zend\Stdlib\CallbackHandler', $listener1);
        $this->assertSame($this->object, $listener1->getCallback()[0]);
        $this->assertEquals('onBootstrap', $listener1->getCallback()[1]);

        $onRouteListners = $eventManager->getListeners(MvcEvent::EVENT_ROUTE);
        $this->assertCount(1, $onRouteListners);
        $listener2 = $onRouteListners->getIterator()->current();
        $this->assertInstanceOf('Zend\Stdlib\CallbackHandler', $listener2);
        $this->assertSame($this->object, $listener2->getCallback()[0]);
        $this->assertEquals('onRoute', $listener2->getCallback()[1]);
    }

    public function testAttachTwice()
    {
        $eventManager = new EventManager;
        $eventManager->attachAggregate($this->object);
        $eventManager->attachAggregate($this->object);
        $onBootstrapListners = $eventManager->getListeners(MvcEvent::EVENT_BOOTSTRAP);
        $this->assertCount(1, $onBootstrapListners);
        $onRouteListners = $eventManager->getListeners(MvcEvent::EVENT_ROUTE);
        $this->assertCount(1, $onRouteListners);
    }

    /**
     * @covers Flower\Domain\Service::detach
     */
    public function testDetach()
    {
        $eventManager = new EventManager;
        $eventManager->attachAggregate($this->object);
        $listeners = array();
        $listeners[] = $eventManager->getListeners(MvcEvent::EVENT_BOOTSTRAP)->getIterator()->current();
        $listeners[] = $eventManager->getListeners(MvcEvent::EVENT_ROUTE)->getIterator()->current();
        $this->assertEquals($listeners, array_values(TestTool::getPropertyValue($this->object, 'listeners')));
        $this->object->detach($eventManager);
        $this->assertEmpty(TestTool::getPropertyValue($this->object, 'listeners'));
        $this->assertTrue($eventManager->getListeners(MvcEvent::EVENT_BOOTSTRAP)->isEmpty());
        $this->assertTrue($eventManager->getListeners(MvcEvent::EVENT_ROUTE)->isEmpty());
    }
}
