<?php
namespace Flower\Form\View;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\Form\FormUtilsGumby;
use Zend\Form\Element\Button;

class RenderStrategyGumbyPane extends RenderStrategy
{

    protected $showValues;

    /**
     * commonly use for preview mode
     *
     * @param boolean $showValues
     */
    public function setShowValues($showValues = true)
    {
        $this->showValues = (bool) $showValues;
    }

    public function prepare($element, $view)
    {
        $this->element = $element;
        $this->view = $view;
        FormUtilsGumby::addAttributes($element);
        $this->element->prepare();
    }

    public function renderElement($element)
    {
        if ($element->getMessages()) {
            $renderer = $this->view->pane(FormUtilsGumby::getGumbyErrorElementPane());
        } elseif ($this->showValues) {
            $renderer = $this->view->pane(FormUtilsGumby::getGumbyElementResultPane());
        } elseif ($element instanceof Button) {
            $renderer = $this->view->pane(FormUtilsGumby::getGumbyButtonPane());
        } else {
            $renderer = $this->view->pane(FormUtilsGumby::getGumbyElementPane());
        }

        $renderer->setVar('element', $element);
        //ここでペインに設定を施すことも可能だな。$varにクロージャーを設定するとか。
        echo $renderer;
    }
}