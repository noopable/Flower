<?php
namespace Flower\Form\View;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\Form\FormUtils;

class RenderStrategyTwPane extends RenderStrategy
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
        FormUtils::addTwAttributes($element);
        $this->element->prepare();
    }

    public function renderElement($element)
    {
        if ($element->getMessages()) {
            $renderer = $this->view->pane(FormUtils::getTwErrorElementPane());
        } elseif ($this->showValues) {
            $renderer = $this->view->pane(FormUtils::getTwElementResultPane());
        } else {
            $renderer = $this->view->pane(FormUtils::getTwElementPane());
        }

        $renderer->setVar('element', $element);
        //ここでペインに設定を施すことも可能だな。$varにクロージャーを設定するとか。
        echo $renderer;
    }
}