<?php
namespace Flower\Form;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Form\Form;

abstract class AbstractForm extends Form
{
        /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        if (null === $name) {
            $name = get_called_class();
        }
        parent::__construct($name, $options);

    }

    public function init()
    {
        $this->build();
    }

    abstract protected function build();

    public function __sleep()
    {
        $object_vars = get_object_vars($this);
        unset($object_vars['factory']);
        return array_keys($object_vars);
    }

}