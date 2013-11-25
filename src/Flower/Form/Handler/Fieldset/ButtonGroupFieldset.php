<?php
namespace Flower\Form\Handler\Fieldset;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Form\Fieldset;
/**
 * Description of ButtonGroupFieldset
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ButtonGroupFieldset extends Fieldset implements ButtonGroupInterface
{

    protected $class = 'foo';

    protected $buttons = array(
        'button',
        'next',
        'back',
        'confirm',
        'reset',
        'cancel',
        'submit',
    );

    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     */
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        if (isset($options['buttons'])) {
            foreach ($options['buttons'] as $button) {
                $button = strtolower($button);
                if (in_array($button, $this->buttons)) {
                    $method = 'add' . ucfirst($button);
                    call_user_func(array($this, $method));
                }
            }
        }

    }

    public function addButton($name = null)
    {

    }

    public function addNext($name = 'submit_next')
    {
        $this->add(array(
            'name' => $name,
            'attributes' => array(
                'class' => 'btn btn-primary btn-large',
                'type'  => 'submit',
                'value' => ' 　次へ 　',
                'id' => 'submit_next',
            ),
        ));
    }

    public function addBack($name = 'submit_back')
    {
        $this->add(array(
            'name' => $name,
            'attributes' => array(
                'class' => 'btn btn-large',
                'type'  => 'submit',
                'value' => ' 　戻る 　',
                'id' => 'submit_back',
            ),
        ));
    }
}
