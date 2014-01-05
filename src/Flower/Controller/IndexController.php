<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Controller;

use Zend\Mvc\Controller\AbstractActionController;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $fileAdapter = $this->getServiceLocator()->get('Flower_File_Adapter');
        $data = $fileAdapter->read('sample');
        if (null === $data) {
            $event = $fileAdapter->getLastEvent();
            $namedFiles = $fileAdapter->getLastEvent()->getNamedFiles();
            if ($namedFiles->count() === 0) {
                var_dump($event->getStates());
            }
            
        }
        return array('message' => 'this is index<br />' . $data);
    }

    public function dicheckAction()
    {
        $sl = $this->getServiceLocator();
        $di = $sl->get('di');
        //$definitionList = $di->definitions();
        return array('di' => $di);
    }
    
    public function diArrayOutputAction()
    {
        $runtimeExporter = new \Flower\RuntimeDefinitionExporter();
        return array('runtimeExporter' => $runtimeExporter);
        
    }
}