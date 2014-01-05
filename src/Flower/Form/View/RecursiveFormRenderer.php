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

class RecursiveFormRenderer extends RecursiveIteratorIterator
{
    /**
     * rendering strategy object
     * @var renderer
     */
    protected $renderStrategy;

    protected $form;

    protected $view;

    public function __construct(RecursiveForm $form, RenderStrategyInterface $renderStrategy = null, View $view = null)
    {
        if (null !== $view) {
            $this->view = $view;
        }

        $this->form = $form->getForm();

        parent::__construct($form);

        $this->renderStrategy = $renderStrategy ?: new RenderStrategy;
        $this->renderStrategy->setRecursiveIteratorIterator($this);
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function getView()
    {
        return $this->view;
    }

    public function setRenderStrategy($strategy)
    {
        $strategy->setRecursiveIteratorIterator($this);
        $this->renderStrategy = $strategy;
    }

    public function getRenderStrategy()
    {
        return $this->renderStrategy;
    }

    public function beginIteration()
    {
        $this->renderStrategy->beginIteration();
    }

    public function endIteration()
    {
        $this->renderStrategy->endIteration();
    }

    public function beginChildren()
    {
        //フィールドセットの開始
        $this->renderStrategy->beginChildren();
    }
    public function endChildren()
    {
        //フィールドセットの終了
        $this->renderStrategy->endChildren();
    }
    public function current()
    {
        //エレメントの型に応じたレンダリング
        $this->renderStrategy->renderElement(parent::current());
        return parent::current();
    }

    public function nextElement()
    {
        $this->renderStrategy->renderSeparetor();
    }

    public function __toString()
    {
        $this->renderStrategy->prepare($this->form, $this->getView());
        ob_start();
            $this->rewind();
            while ($this->valid()) { $this->current();$this->next();}
        return ob_get_clean();
    }
}