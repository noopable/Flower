<?php
namespace FlowerTest\View\Pane\ManagerListener;

use Flower\Test\TestTool;
use Flower\View\Pane\PaneClass\Pane;
use Flower\View\Pane\PaneEvent;
use Flower\View\Pane\ManagerListener\PaneCacheListener;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-07 at 11:03:24.
 */
class PaneCacheListenerConcreteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PaneCacheListener
     */
    protected $object;

    protected $config;

    protected $storageOptions;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new PaneCacheListener;
        $this->config = require 'TestAsset/pane_cache_listener.config.php';
        $this->storageOptions = $this->config['pane_cache_listener']['cache_storage'];
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testPostGetConcreteCache()
    {
        $paneId = 'foo';
        $pane = new Pane;
        $event = new PaneEvent(PaneEvent::EVENT_GET_PANE);
        $event->setPaneId($paneId);
        $event->setResult($pane);

        $this->object->setStorageOptions($this->storageOptions);
        $res = $this->object->postGet($event);
        $this->assertSame($pane, $res);

        $storage = $this->object->getStorage();
        $storage->removeItem('foo');
    }

    public function testPreGetConcreteCache()
    {
        $paneId = 'preset';
        $event = new PaneEvent(PaneEvent::EVENT_GET_PANE);
        $event->setPaneId($paneId);
        $event->setTarget($paneId);

        $this->object->setStorageOptions($this->storageOptions);
        $res = $this->object->preGet($event);
        $this->assertInstanceOf('Flower\View\Pane\PaneClass\Pane', $res);
    }
}
