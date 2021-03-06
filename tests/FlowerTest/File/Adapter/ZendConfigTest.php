<?php

namespace FlowerTest\File\Adapter;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\File\Adapter\ZendConfig;
use Flower\File\Event;
use Flower\File\FileInfo;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-04-17 at 14:14:42.
 */
class ZendConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ZendConfig
     */
    protected $object;

    protected $tmpDir;
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ZendConfig;
        $this->tmpDir = realpath(__DIR__ . '/../TestAsset/tmp');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * @covers Flower\File\Adapter\ZendConfig::configure
     * @todo   Implement testConfigure().
     */
    public function testConfigure()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\File\Adapter\ZendConfig::onRead
     * @todo   Implement testOnRead().
     */
    public function testOnRead()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\File\Adapter\ZendConfig::onWrite
     */
    public function testOnWrite()
    {
        $event = new Event('write');
        $event->setDataName('tmp');
        $event->setData(array('foo' => 'bar'));
        $fileName = $this->tmpDir . '/' . $event->getDataName() . '.default.php';
        $fileInfo = new FileInfo($fileName);
        $fileInfo->setValue($event->getData());
        
        $namedFiles = $event->getNamedFiles();
        $namedFiles->setFile($fileInfo);
        
        $this->assertEquals(1, $namedFiles->count());
        
        
        $affected = $this->object->onWrite($event);
        $this->assertGreaterThan(0, $affected);
    }

    /**
     * @covers Flower\File\Adapter\ZendConfig::onMerge
     * @todo   Implement testOnMerge().
     */
    public function testOnMerge()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
