<?php
namespace Flower\Form\View;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\Form\FormUtils;
use Flower\Form\Handler\Fieldset\ButtonGroupInterface;
/**
 * Description of RenderStrategyTwPaneWithButtonGroup
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class RenderStrategyTwPaneWithButtonGroup extends RenderStrategyTwPane
{
    public function renderElement($element)
    {
        $recursiveForm = $this->getInnerIterator();
        $current = $recursiveForm->current();
        $fieldset = $recursiveForm->getForm();
        if ($fieldset instanceof ButtonGroupInterface) {
            echo $this->view->formRow($element);
        } else {
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

    public function beginChildren()
    {
        //フィールドセットの開始
        $recursiveForm = $this->getInnerIterator();
        $current = $recursiveForm->current();
        $fieldset = $recursiveForm->getForm();
        if ($fieldset instanceof ButtonGroupInterface) {
            echo "<div class=\"btn-toolbar form-actions\">\n";
        } else {
            echo "<fieldset>\n";
        }
    }
    public function endChildren()
    {
        //フィールドセットの終了
        $recursiveForm = $this->getInnerIterator();
        $current = $recursiveForm->current();
        $fieldset = $recursiveForm->getForm();
        if ($fieldset instanceof ButtonGroupInterface) {
            echo "</div>\n";
        } else {
            echo "</fieldset>\n";
        }
    }

}
