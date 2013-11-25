<?php
namespace Flower\Form\Handler;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Form\FormInterface;
/**
 *
 * in Controller:
 * PRG Post phase
 * $res = $form->getHandler()->handle();
 * return array('form' => $res);
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface FormHandlerInterface
{
    public function setName($name);
    public function getName($fullName = false);

    public function setForm(FormInterface $form);
    public function getForm();
    /**
     *
     * @param array|false $post
     */
    public function setPost($post);
    public function getPost();
    public function setObject($object);
    public function getObject();
    public function handle();
    /**
     *
     * @return string|FormHandlerInterface|Response
     */
    public function processRequest();
    public function prepareForm();
}
