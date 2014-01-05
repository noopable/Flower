<?php
namespace Flower\Form;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use RecursiveIterator;
use Zend\Form;

/**
 *
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
class RecursiveForm implements RecursiveIterator
{
    protected $innerIterator;

    public function __construct(Form\FieldsetInterface $form)
    {
        $this->form = $form;
        $this->innerIterator = $form->getIterator()->getIterator();
    }

    /**
     * current fieldset
     *
     * @return Form\FieldsetInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    public function getInnerIterator()
    {
        return $this->innerIterator;
    }

    public function getChildren()
    {
        if ($this->hasChildren()) {
            return new RecursiveForm($this->current());
        }
    }

    public function hasChildren()
    {
        return ($this->current() instanceof Form\FieldsetInterface);
    }

    public function current()
    {
        return $this->getInnerIterator()->current();
    }

    public function key()
    {
         return $this->getInnerIterator()->key();
    }

    public function next()
    {
         $this->getInnerIterator()->next();
    }

    public function rewind()
    {
        $this->innerIterator = $this->form->getIterator()->getIterator();
        //cloneされて新しいiteratorを取得できる
    }

    public function valid()
    {
         return $this->getInnerIterator()->valid();
    }
}