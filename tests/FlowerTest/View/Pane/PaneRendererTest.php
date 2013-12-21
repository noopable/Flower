<?php

namespace FlowerTest\View;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\View\Pane\PaneRenderer;
use Flower\View\Pane\Pane;
use Flower\View\Pane\Builder;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-04-05 at 19:28:01.
 */
class PaneRendererTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PaneRenderer
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    
    public function testRender()
    {
        $paneConfig = include __DIR__ . '/TestAsset/2column.php';
        $builder =new Builder;
        $pane = $builder->build($paneConfig);
        $paneRenderer = new PaneRenderer($pane);
        $expected = <<<EOD
<!-- begin PaneRenderer -->
<body class='container'>
  <!-- start content header -->
  <div class='container subhead header' id='overview'>
    <!-- var header is not found -->
  </div>
  <!-- end content header -->
  <div class='container'>
    <!-- start content content -->
    <div class='article row pull-right'>
      <!-- var content is not found -->
    </div>
    <!-- end content content -->
    <div style='float:right;' class='sidebar' id='sidebar'>
      <!-- start content sidebar -->
      <div class='span3'>
        <!-- var sidebar is not found -->
      </div>
      <!-- end content sidebar -->
    </div>
  </div>
  <div id='footer'>
    <!-- start content footer -->
    <div class='container'>
      <!-- var footer is not found -->
    </div>
    <!-- end content footer -->
  </div>
</body>

<!-- end PaneRenderer -->   
EOD;
        $this->assertXmlStringEqualsXmlString($expected, (string) $paneRenderer);
    }
    
   public function testRenderWithSize()
    {
        $paneConfig = include __DIR__ . '/TestAsset/2column_size.php';
        $builder =new Builder;
        $pane = $builder->build($paneConfig);
        $paneRenderer = new PaneRenderer($pane);
        $expected = <<<EOD
<!-- begin PaneRenderer -->
<!-- start pane -->
<div class="container">
  <!-- start content header -->
  <div class='container jumbotron subhead header' id='overview'>
    <!-- var header is not found -->
  </div>
  <!-- end content header -->
  <div class='container'>
    <div class='article row pull-right'>
      <div class='span9'>
        <!-- start content categoryImage -->
        <div>
          <!-- var categoryImage is not found -->
        </div>
        <!-- end content categoryImage -->
        <div class='row'>
          <!-- start content content -->
          <div class='span9'>
            <!-- var content is not found -->
          </div>
          <!-- end content content -->
        </div>
      </div>
    </div>
    <div class='sidebar row' id='sidebar'>
      <!-- start content sidebar -->
      <div class='span3'>
        <!-- var sidebar is not found -->
      </div>
      <!-- end content sidebar -->
    </div>
  </div>
  <div id='footer'>
    <!-- start content footer -->
    <div class='container'>
      <!-- var footer is not found -->
    </div>
    <!-- end content footer -->
  </div>
</div>
<!-- end pane -->
<!-- end PaneRenderer -->   
EOD;
        $this->assertXmlStringEqualsXmlString($expected, (string) $paneRenderer);
    }
    
    /**
     * @covers Flower\View\PaneRenderer::setVars
     * @todo   Implement testSetVars().
     */
    public function testSetVars()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::getVars
     * @todo   Implement testGetVars().
     */
    public function testGetVars()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::setVar
     * @todo   Implement testSetVar().
     */
    public function testSetVar()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::setView
     * @todo   Implement testSetView().
     */
    public function testSetView()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::getView
     * @todo   Implement testGetView().
     */
    public function testGetView()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::beginIteration
     * @todo   Implement testBeginIteration().
     */
    public function testBeginIteration()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::endIteration
     * @todo   Implement testEndIteration().
     */
    public function testEndIteration()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::beginChildren
     * @todo   Implement testBeginChildren().
     */
    public function testBeginChildren()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::endChildren
     * @todo   Implement testEndChildren().
     */
    public function testEndChildren()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::current
     * @todo   Implement testCurrent().
     */
    public function testCurrent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Flower\View\PaneRenderer::__toString
     * @todo   Implement test__toString().
     */
    public function test__toString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

}
