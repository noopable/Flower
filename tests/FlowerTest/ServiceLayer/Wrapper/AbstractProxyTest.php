<?php
namespace FlowerTest\ServiceLayer\Wrapper;

use FlowerTest\ServiceLayer\TestAsset\ServiceForTest;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-01-16 at 09:32:37.
 */
class AbstractProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractProxy
     */
    protected $object;
    
    /**
     *
     * @var ServiceForTest
     */
    protected $service;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->service = new ServiceForTest;
        $this->object = new TestAsset\ConcreteProxy($this->service);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Flower\ServiceLayer\Wrapper\AbstractProxy::passThrough
     */
    public function testPassThrough()
    {
        $this->assertSame($this->service, $this->object->passThrough());
    }

    /**
     * @covers Flower\ServiceLayer\Wrapper\AbstractProxy::__call
     */
    public function test__call()
    {
        $res = $this->object->foo('a', 'b', 'c');
        $this->assertEquals('foo', $res['name']);
        $this->assertEquals(array('a', 'b', 'c'), $res['arguments']);
    }
}