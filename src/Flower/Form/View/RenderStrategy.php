<?php
namespace Flower\Form\View;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use RecursiveIteratorIterator;
use Zend\View\Renderer\RendererInterface as View;
use Flower\Form\RecursiveForm;
/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
class RenderStrategy implements RenderStrategyInterface
{
    protected $element;

    protected $view;

    protected $tagStack = array();

    public function setForm(RecursiveForm $form)
    {
        $this->element = $form;
    }

    public function setView(View $view)
    {
        $this->view = $view;
    }

    public function setRecursiveIteratorIterator(RecursiveIteratorIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     *
     * @return \Flower\Form\RecursiveForm
     */
    public function getInnerIterator()
    {
        return $this->iterator->getInnerIterator();
    }

    public function prepare($element, $view)
    {
        $this->element = $element;
        $this->view = $view;

        $this->element->prepare();
    }

    public function beginIteration()
    {
        echo $this->view->form()->openTag($this->element);
    }

    public function endIteration()
    {
        echo $this->view->form()->closeTag();
    }

    public function beginChildren()
    {
        //フィールドセットの開始
        echo "<fieldset>\n";
    }
    public function endChildren()
    {
        //フィールドセットの終了
        echo "</fieldset>\n";
    }
    public function renderElement($element)
    {
        //エレメントの型に応じたレンダリング
        echo $this->view->formRow($element);
    }

    public function renderSeparetor()
    {
        //エレメント間の共通のdivider
        //echo "<span class=\"divider\"></span>";
    }
}