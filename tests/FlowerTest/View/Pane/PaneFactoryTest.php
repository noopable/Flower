<?php
namespace FlowerTest\View\Pane;

use Flower\View\Pane\Builder;
use Flower\View\Pane\PaneFactory;
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-01 at 09:47:45.
 */
class PaneFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Flower\View\Pane\PaneFactory::factory
     */
    public function testFactoryStandard()
    {
        $builder = new Builder;
        $paneConfig = array(
            'tag' => $tag = 'foo',
            'id' => $id = '30-cc2',
            'order' => $order = 5,
            'size' => $size = 10,
            'var' => $var = 'header',
            'classes' => $classes = 'container row',
            'attributes' => $attributes = array(
                'foo' => 'bar',
                'baz' => 'qux',
            ),
            /*
             * normally autodetect
            'pane_class' => 'FlowerTest\View\Pane\TestAsset\YetAnotherPane',
            'begin' => 'foo',
            'end' => 'bar',
            */
        );

        $pane = PaneFactory::factory($paneConfig, $builder);
        $this->assertInstanceOf('Flower\View\Pane\Pane', $pane);
        $this->assertEquals($tag, $pane->tag);
        $this->assertEquals($tag, $pane->wrapTag, 'if wrapTag is omitted ,wrapTag is same to tag');
        $this->assertEquals('cc2', $pane->id);
        $this->assertEquals($order, $pane->order);
        $this->assertEquals($size, $pane->size);
        $this->assertEquals($var, $pane->var);
        $this->assertEquals('<foo foo="bar" baz="qux" id="cc2" class="span10 container row">', trim($pane->begin()));
        $this->assertEquals('<foo foo="bar" baz="qux" id="cc2" class="span10 container row">', trim($pane->wrapBegin()));
        $this->assertEquals('</foo>', $pane->end());
        $this->assertEquals('</foo>', $pane->wrapEnd());
        $this->assertEquals(explode(' ', $classes), $pane->classes);
        $this->assertEquals($attributes, $pane->attributes);
    }

    /**
     * @covers Flower\View\Pane\PaneFactory::factory
     */
    public function testFactoryCustom1()
    {
        $builder = new Builder;
        $paneConfig = array(
            'pane_class' => 'FlowerTest\View\Pane\TestAsset\YetAnotherPane',
            'tag' => $tag = 'foo',
            'order' => $order = 5,
            'size' => $size = 10,
            'var' => $var = 'header',
            'classes' => $classes = 'container row',
            'attributes' => $attributes = array(
                'foo' => 'bar',
                'baz' => 'qux',
            ),
            /*
             * normally autodetect
            'begin' => 'foo',
            'end' => 'bar',
            */
        );

        $pane = PaneFactory::factory($paneConfig, $builder);
        $this->assertInstanceOf('FlowerTest\View\Pane\TestAsset\YetAnotherPane', $pane);
        $this->assertEquals($tag, $pane->tag);
        $this->assertEquals($order, $pane->order);
        $this->assertEquals($size, $pane->size);
        $this->assertEquals($var, $pane->var);
        $this->assertEquals(explode(' ', $classes), $pane->classes);
        $this->assertEquals($attributes, $pane->attributes);
    }

    /**
     * @covers Flower\View\Pane\PaneFactory::factory
     */
    public function testFactoryCustomBeginEnd()
    {
        $builder = new Builder;
        $paneConfig = array(
            'tag' => $tag = 'foo',
            'order' => $order = 5,
            'size' => $size = 10,
            'var' => $var = 'header',
            'classes' => $classes = 'container row',
            'attributes' => $attributes = array(
                'foo' => 'bar',
                'baz' => 'qux',
            ),
            'begin' => 'foo',
            'end' => 'bar',
            /*
             * normally autodetect
            'pane_class' => 'FlowerTest\View\Pane\TestAsset\YetAnotherPane',
            */
        );

        $pane = PaneFactory::factory($paneConfig, $builder);
        $this->assertInstanceOf('Flower\View\Pane\Pane', $pane);
        $this->assertEquals($tag, $pane->tag);
        $this->assertEquals($order, $pane->order);
        $this->assertEquals($size, $pane->size);
        $this->assertEquals($var, $pane->var);
        $this->assertEquals(explode(' ', $classes), $pane->classes);
        $this->assertEquals($attributes, $pane->attributes);
        $this->assertEquals('foo', $pane->begin());
        $this->assertEquals('bar', $pane->end());
        //begin endの指定とwrapBegin wrapEndは相互に影響しない。
        $this->assertEquals('<foo foo="bar" baz="qux" class="span10 container row">', trim($pane->wrapBegin()));
        $this->assertEquals('</foo>', $pane->wrapEnd());
    }

    /**
     * @covers Flower\View\Pane\PaneFactory::factory
     */
    public function testFactoryCustomWrapBeginEnd()
    {
        $builder = new Builder;
        $paneConfig = array(
            'tag' => $tag = 'foo',
            'order' => $order = 5,
            'size' => $size = 10,
            'var' => $var = 'header',
            'classes' => $classes = 'container row',
            'attributes' => $attributes = array(
                'foo' => 'bar',
                'baz' => 'qux',
            ),
            'wrapBegin' => 'foo',
            'wrapEnd' => 'bar',
            /*
             * normally autodetect
            'pane_class' => 'FlowerTest\View\Pane\TestAsset\YetAnotherPane',
            */
        );

        $pane = PaneFactory::factory($paneConfig, $builder);
        //begin endの指定とwrapBegin wrapEndは相互に影響しない。
        $this->assertEquals('<foo foo="bar" baz="qux" class="span10 container row">', trim($pane->begin()));
        $this->assertEquals('</foo>', $pane->end());

        $this->assertEquals('foo', $pane->wrapBegin());
        $this->assertEquals('bar', $pane->wrapEnd());
    }

    /**
     * @covers Flower\View\Pane\PaneFactory::factory
     */
    public function testFactoryEscaper()
    {
        $builder = new Builder;
        $paneConfig = array(
            'tag' => $tag = 'fo?"o',
            'id' => $id = '30-c\c3',
            'order' => $order = '5x',
            'size' => $size = '10--',
            'classes' => $classes = 'container row 2002\'s"',
            'attributes' => $attributes = array(
                'fo\o-1' => 'bar"',
                'ba&z2' => 'qux\'',
            ),
        );

        $pane = PaneFactory::factory($paneConfig, $builder);
        $this->assertInstanceOf('Flower\View\Pane\Pane', $pane);
        $this->assertEquals('foo', $pane->tag);
        $this->assertEquals('cc3', $pane->id);
        $this->assertEquals(5, $pane->order);
        $this->assertEquals(10, $pane->size);
        $this->assertEquals('<foo foo-1="bar&quot;" baz2="qux&#x27;" id="cc3" class="span10 container row 2002&#x27;s&quot;">', trim($pane->begin()));
        $this->assertEquals('</foo>', $pane->end());
        $this->assertEquals(explode(' ', $classes), $pane->classes);
        $this->assertEquals($attributes, $pane->attributes);
    }

    /**
     * @covers Flower\View\Pane\Builder::getEscaper
     */
    public function testGetEscaper()
    {
        $this->assertInstanceOf('Zend\Escaper\Escaper', PaneFactory::getEscaper());
        $this->assertInstanceOf('Zend\Escaper\Escaper', PaneFactory::getEscaper(), '大事なので２度確認しました');
    }
}
