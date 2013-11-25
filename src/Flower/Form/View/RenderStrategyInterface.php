<?php
namespace Flower\Form\View;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use RecursiveIteratorIterator;
use Zend\View\Renderer\RendererInterface as View;
use Flower\Form\RecursiveForm;

interface RenderStrategyInterface
{
    public function setForm(RecursiveForm $form);
    public function setView(View $view);
    public function setRecursiveIteratorIterator(RecursiveIteratorIterator $iterator);
    public function prepare($element, $view);
    public function beginIteration();
    public function endIteration();
    public function beginChildren();
    public function endChildren();
    public function renderElement($element);
    public function renderSeparetor();
}