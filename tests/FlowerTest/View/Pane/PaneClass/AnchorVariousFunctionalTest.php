<?php
namespace FlowerTest\View\Pane\PaneClass;

use Flower\Test\TestTool;
use FlowerTest\Bootstrap;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Renderer\PhpRenderer;

/**
 * rendering文字列中に下記が現れる場合
* RouteMatch does not contain a matched route name
*  RouteMatchにmatchedRouteNameが設定されていない場合
*
*
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-02-01 at 22:15:54.
 */
class AnchorVariousFunctionalTest extends \PHPUnit_Framework_TestCase
{
    protected $serviceLocator;

    protected $router;

    protected $helper;

    protected $view;

    public function setUp()
    {
        $config = Bootstrap::getServiceManager()->get('ApplicationConfig');
        $serviceManager = new ServiceManager(new ServiceManagerConfig());
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();
        $serviceManager->get('Application')->bootstrap();
        $this->serviceLocator = $serviceManager;
        $this->router = $this->serviceLocator->get('HttpRouter');
        $params = array(
            'controller' => 'test',
            'action' => 'index',
            'foo' => 'bar',
        );
        $routeMatch = new RouteMatch($params);
        $routeMatch->setMatchedRouteName('application/default');
        $application = $this->serviceLocator->get('Application');
        $application->getMvcEvent()->setRouteMatch($routeMatch);
        $this->helperManager = $this->serviceLocator->get('ViewHelperManager');
        $this->view = new PhpRenderer;
        $this->view->setHelperPluginManager($this->helperManager);
        $this->helper = $this->view->plugin('npPaneManager');
        $this->helper->setBuilderMode('anchor');
    }

    public function testEnv()
    {
        $renderer = $this->helperManager->getRenderer();
        $this->assertInstanceOf('Zend\View\Renderer\PhpRenderer', $renderer);

        $this->assertInstanceOf('Flower\View\Pane\PaneManager', $this->helper);

        $builder = $this->helper->getBuilder();
        $this->assertEquals('Flower\View\Pane\PaneClass\Anchor', TestTool::getPropertyValue($builder, 'paneClass'));
    }

    public function testUseLabel()
    {
        $expected =
'<!-- begin Renderer -->
<ul>
<li>
  <span class="container">Link Label 1</span>
</li>
</ul>
<!-- end Renderer -->
';
        $expected = str_replace("\r\n","\n", $expected);
        $paneConfig = array(
            'classes' => 'container',
            'label' => 'Link Label 1',
        );
        $this->helper->setPaneConfig('foo', $paneConfig);
        $res = $this->helper->render('foo');
        $this->assertEquals($expected, str_replace("\r\n","\n", $res));
    }

}