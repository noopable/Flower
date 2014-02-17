<?php
namespace FlowerTest\View\Pane;

use ArrayObject;
use Flower\Test\TestTool;
use Flower\View\Pane\Builder\Builder;
use Flower\View\Pane\PaneClass\Pane;
use Flower\View\Pane\PaneClass\PaneInterface;
use Flower\View\Pane\PaneEvent;
use Flower\View\Pane\PaneManager;
use Zend\EventManager\EventManager;
use Zend\View\Renderer\PhpRenderer;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-05 at 20:20:23.
 */
class PaneManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PaneManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PaneManager;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\View\Pane\PaneManager::__invoke
     */
    public function test__invoke()
    {
        $this->assertSame($this->object, $this->object->__invoke());
        $this->assertTrue(TestTool::getPropertyValue($this->object, 'init'));
    }

    /**
     * @covers Flower\View\Pane\PaneManager::init
     */
    public function testInit()
    {
        $this->assertFalse(TestTool::getPropertyValue($this->object, 'init'));
        $this->assertTrue(TestTool::getPropertyValue($this->object, 'defaultListenersWait'));
        $this->object->init();
        $eventManager = $this->object->getEventManager();
        $listeners = $eventManager->getListeners(PaneEvent::EVENT_BUILD_PANE);
        $this->assertCount(1, $listeners);
        $events = $eventManager->getEvents();
        $this->assertCount(4, $events);
        $this->assertTrue(TestTool::getPropertyValue($this->object, 'init'));
        $this->assertFalse(TestTool::getPropertyValue($this->object, 'defaultListenersWait'));

        //call twice same result
        $this->object->init();
        $listeners = $eventManager->getListeners(PaneEvent::EVENT_BUILD_PANE);
        $this->assertCount(1, $listeners);
        $events = $eventManager->getEvents();
        $this->assertCount(4, $events);
        $this->assertTrue(TestTool::getPropertyValue($this->object, 'init'));
        $this->assertFalse(TestTool::getPropertyValue($this->object, 'defaultListenersWait'));
    }

    /**
     * @covers Flower\View\Pane\PaneManager::setConfig
     */
    public function testSetConfig()
    {
        $config = array(
            'foo' => array('class' => 'container'),
            'bar' => array('class' => 'content', 'inner' => array('tag' => 'div')),
        );
        $this->object->setConfig($config);
        $this->assertEquals($config, TestTool::getPropertyValue($this->object, 'config'));
    }

    /**
     * @depends testSetConfig
     * @covers Flower\View\Pane\PaneManager::getConfig
     */
    public function testGetConfig()
    {
        $config = array(
            'foo' => array('class' => 'container'),
            'bar' => array('class' => 'content', 'inner' => array('tag' => 'div')),
        );
        $this->object->setConfig($config);
        $this->assertEquals($config, $this->object->getConfig());
    }

    /**
     * @covers Flower\View\Pane\PaneManager::setPaneConfig
     */
    public function testSetPaneConfig()
    {
        $config = array(
            'foo' => array('class' => 'container'),
            'bar' => array('class' => 'content', 'inner' => array('tag' => 'div')),
        );
        $this->object->setConfig($config);
        $this->object->setPaneConfig('baz', array('class' => 'sidebar'));
        $config['baz'] = array('class' => 'sidebar');
        $this->assertEquals($config, TestTool::getPropertyValue($this->object, 'config'));
    }

    /**
     * @depends testSetConfig
     * @covers Flower\View\Pane\PaneManager::getPaneConfig
     */
    public function testGetPaneConfig()
    {
        $config = array(
            'foo' => array('class' => 'container'),
            'bar' => array('class' => 'content', 'inner' => array('tag' => 'div')),
        );
        $this->object->setConfig($config);
        $this->assertEquals(array('class' => 'container'), $this->object->getPaneConfig('foo'));
    }

    /**
     * @covers Flower\View\Pane\PaneManager::onLoadConfig
     */
    public function testOnLoadConfig()
    {
        $paneId = 'foo';
        $paneConfig = array('class' => 'container');
        $manager = $this->getMock('Flower\View\Pane\PaneManager');
        $config = array(
            $paneId => $paneConfig,
            'bar' => array('class' => 'content', 'inner' => array('tag' => 'div')),
        );
        $this->object->setConfig($config);

        $loadEvent = new PaneEvent(PaneEvent::EVENT_LOAD_CONFIG);
        $loadEvent->setManager($manager);
        $loadEvent->setPaneId($paneId);
        $loadEvent->setTarget($paneId);

        $res = $this->object->onLoadConfig($loadEvent);
        $this->assertEquals($paneConfig, $res);
        $this->assertEquals($paneConfig, $loadEvent->getResult());
    }

    /**
     * @covers Flower\View\Pane\PaneManager::setBuilder
     */
    public function testSetBuilder()
    {
        $builder = new Builder;
        $this->object->setBuilder($builder);
        $this->assertSame($builder, TestTool::getPropertyValue($this->object, 'builder'));
    }

    /**
     * @depends testSetBuilder
     * @covers Flower\View\Pane\PaneManager::getBuilder
     */
    public function testGetBuilder()
    {
        $builder = new Builder;
        $this->object->setBuilder($builder);
        $this->assertSame($builder, $this->object->getBuilder());
    }

    /**
     * @covers Flower\View\Pane\PaneManager::setRendererClass
     */
    public function testSetRendererClass()
    {
        $class = 'Flower\View\Pane\PaneRenderer';
        $this->object->setRendererClass($class);
        $this->assertEquals($class, TestTool::getPropertyValue($this->object, 'rendererClass'));
    }

    /**
     * @depends testSetRendererClass
     * @covers Flower\View\Pane\PaneManager::getRendererClass
     */
    public function testGetRendererClass()
    {
        $class = 'Flower\View\Pane\PaneRenderer';
        $this->object->setRendererClass($class);
        $this->assertEquals($class, $this->object->getRendererClass());
    }

    /**
     * @covers Flower\View\Pane\PaneManager::attachDefaultListers
     */
    public function testAttachDefaultListers()
    {
        $this->object->attachDefaultListers();
        $eventManager = $this->object->getEventManager();
        $listeners = $eventManager->getListeners(PaneEvent::EVENT_BUILD_PANE);
        $this->assertCount(1, $listeners);
        $events = $eventManager->getEvents();
        $this->assertCount(4, $events);
        $this->assertFalse(TestTool::getPropertyValue($this->object, 'defaultListenersWait'));
    }

    /**
     * @covers Flower\View\Pane\PaneManager::setRegistry
     */
    public function testSetRegistry()
    {
        $registry = new ArrayObject(array('foo' => 'bar'), ArrayObject::ARRAY_AS_PROPS);
        $this->object->setRegistry($registry);
        $this->assertSame($registry, TestTool::getPropertyValue($this->object, 'registry'));
    }

    /**
     * @depends testSetRegistry
     * @covers Flower\View\Pane\PaneManager::getRegistry
     */
    public function testGetRegistry()
    {
        $default = $this->object->getRegistry();
        $this->assertInstanceOf('ArrayObject', $default);
        $this->assertEquals(ArrayObject::ARRAY_AS_PROPS, $default->getFlags());
        $registry = new ArrayObject(array('foo' => 'bar'), ArrayObject::ARRAY_AS_PROPS);
        $this->object->setRegistry($registry);
        $this->assertSame($registry, $this->object->getRegistry());
    }

    /**
     * @expectedException Flower\View\Pane\Exception\RuntimeException
     */
    public function testCreatePaneRendererWithoutPrepare()
    {
        $pane = new Pane;
        $this->object->createPaneRenderer($pane);
    }

    /**
     * @covers Flower\View\Pane\PaneManager::createPaneRenderer
     */
    public function testCreatePaneRenderer()
    {
        $view = new PhpRenderer;
        $this->object->setView($view);
        $pane = new Pane;
        $renderer = $this->object->createPaneRenderer($pane);
        $this->assertInstanceOf('Flower\View\Pane\PaneRenderer', $renderer);
    }

    /**
     * @covers Flower\View\Pane\PaneManager::setEventManager
     */
    public function testSetEventManager()
    {
        $eventManager = new EventManager;
        $this->object->setEventManager($eventManager);
        $this->assertSame($eventManager, TestTool::getPropertyValue($this->object, 'events'));
        $identifiers = $eventManager->getIdentifiers();
        $this->assertContains(TestTool::getPropertyValue($this->object, 'eventIdentifier'), $identifiers);
    }

    /**
     * @depends testSetEventManager
     * @covers Flower\View\Pane\PaneManager::getEventManager
     */
    public function testGetEventManager()
    {
        $eventManager = new EventManager;
        $this->object->setEventManager($eventManager);
        $this->assertSame($eventManager, $this->object->getEventManager());
    }

    /**
     * @covers Flower\View\Pane\PaneManager::build
     */
    public function testBuild()
    {
        $paneId = 'foo';
        $paneConfig = array('class' => 'container');
        $config = array(
            $paneId => $paneConfig,
            'bar' => array('class' => 'content', 'inner' => array('tag' => 'div')),
        );
        $this->object->setConfig($config);
        $pane = $this->object->build($paneId);
        $this->assertInstanceOf('Flower\View\Pane\PaneClass\Pane', $pane);
    }

    /**
     * @depends testBuild
     * @covers Flower\View\Pane\PaneManager::get
     */
    public function testGet()
    {
        $paneId = 'foo';
        $paneConfig = array('class' => 'container');
        $config = array(
            $paneId => $paneConfig,
            'bar' => array('class' => 'content', 'inner' => array('tag' => 'div')),
        );
        $this->object->setConfig($config);

        $pane = $this->object->get($paneId);
        $this->assertInstanceOf('Flower\View\Pane\PaneClass\Pane', $pane);

        $registry = $this->object->getRegistry();
        $this->assertSame($pane, $registry->$paneId);
        $this->assertSame($pane, $this->object->get($paneId), 'same object. not rebuild nor clone');
    }

    /**
     *
     * @expectedException Flower\View\Pane\Exception\RuntimeException
     */
    public function testGetWithInvalidParamThrowsException()
    {
        $this->object->get(array());
    }

    /**
     * @depends testGet
     * @covers Flower\View\Pane\PaneManager::render
     */
    public function testRender()
    {
        $view = new PhpRenderer;
        $this->object->setView($view);
        $paneId = 'foo';
        $paneConfig = array(
            'class' => 'container',
            'pane_class' => 'Flower\View\Pane\PaneClass\ListPane',
            'tag' => 'div',
            'var' => 'content',
            'inner' => array(
                'class' => 'inner',
                'pane_class' => 'Flower\View\Pane\PaneClass\ListPane',
            ),
        );
        $expected =
'<!-- begin Renderer -->
<ul>
<li>
  <div>
  <!-- start content content -->
    <!-- var content is not found -->
  <!-- end content content -->
  </div>
<ul>
  <!-- start content CallbackRender -->
  <li>
  <span>
  <!-- start content content -->
    <!-- var content is not found -->
  <!-- end content content -->
  </span>
  </li>
  <!-- end content CallbackRender -->
</ul>
</li>
</ul>
<!-- end Renderer -->
';
        $config = array(
            $paneId => $paneConfig,
            'bar' => array('class' => 'content', 'inner' => array('tag' => 'div')),
        );
        $this->object->setConfig($config);
        $res = $this->object->render($paneId);
        $this->assertEquals(str_replace("\r\n", "\n", $expected), $res);
    }

    public function testRefresh()
    {
        $paneId = 'foo';
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $eventManager->expects($this->exactly(3))
                ->method('trigger')
                ->with($this->isInstanceOf('Flower\View\Pane\PaneEvent'));
        $this->object->setEventManager($eventManager);
        $this->object->refresh($paneId);
    }

    public function testRefreshBySpecifiedEventString()
    {
        $paneId = 'foo';
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $eventManager->expects($this->once())
                ->method('trigger')
                ->with($this->isInstanceOf('Flower\View\Pane\PaneEvent'));
        $this->object->setEventManager($eventManager);
        $this->object->refresh($paneId, PaneEvent::EVENT_REFRESH_PANE);
    }

    public function testRefreshBySpecifiedEventArray()
    {
        $paneId = 'foo';
        $eventManager = $this->getMock('Zend\EventManager\EventManager');
        $eventManager->expects($this->exactly(2))
                ->method('trigger')
                ->with($this->isInstanceOf('Flower\View\Pane\PaneEvent'));
        $this->object->setEventManager($eventManager);
        $this->object->refresh($paneId, array(PaneEvent::EVENT_REFRESH_CONFIG, PaneEvent::EVENT_REFRESH_PANE));
    }
}
