<?php
namespace FlowerTest\Domain;

use Flower\Domain\Domain;
use Flower\Domain\DomainStringify;
use Flower\Test\TestTool;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-13 at 11:46:31.
 */
class DomainStringifyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainStringify
     */
    protected $object;


    public function test__construct()
    {
        $domain = new Domain;
        $domain->setDomainId(123);
        $domain->setDomainName('example.com');
        $type = 'domainId';

        $this->object = new DomainStringify($domain, $type);
        $this->assertSame($domain, TestTool::getPropertyValue($this->object, 'domain'));
        $this->assertEquals($type, TestTool::getPropertyValue($this->object, 'type'));
    }

    /**
     * @covers Flower\Domain\DomainStringify::__toString
     */
    public function test__toString()
    {
        $domain = new Domain;
        $domain->setDomainId(123);
        $domain->setDomainName('example.com');

        $this->object = new DomainStringify($domain);
        $this->assertEquals('example.com', (string) $this->object);
    }

    public function test__toStringDomainId()
    {
        $domain = new Domain;
        $domain->setDomainId(123);
        $domain->setDomainName('example.com');

        $this->object = new DomainStringify($domain, 'domainId');
        $this->assertEquals('123', (string) $this->object);
    }
}