<?php
namespace Flower\Form\Handler;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\ServiceManager\ServiceLocatorAwareInterface;
/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface DelegateHandlerInterface extends ServiceLocatorAwareInterface
{
    public function createDelegater($name);
    public function delegate(FormHandlerInterface $formHandler);
}
