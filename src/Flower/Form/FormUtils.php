<?php
namespace Flower\Form;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

class FormUtils
{
    public static function addTwAttributes($form)
    {
        foreach ($form as $element) {
            if ($element instanceof \Zend\Form\Fieldset) {
                self::addTwAttributes($element);
                continue;
            }
            $labelAttributes = $element->getLabelAttributes();
            if (isset($labelAttributes['class'])) {
                if (false === strpos('control-label', $labelAttributes['class'])) {
                    $labelAttributes['class'] .= ' control-label';
                }
            }
            else {
                $labelAttributes['class'] = 'control-label';
            }
            $element->setLabelAttributes($labelAttributes);
        }

        if ($form instanceof \Zend\Form\Form) {
            if ($class = $form->getAttribute('class')) {
                if (false === strpos('form-horizontal', $class)) {
                    $form->setAttribute('class', $class . ' form-horizontal');
                }
            }
            else {
                $form->setAttribute('class', 'form-horizontal');
            }
        }

        return $form;
    }

    public static function getTwErrorElementPane()
    {
        $pane = self::getTwElementPane();
        $pane['classes'] = array('control-group', 'error');
        return $pane;
    }

    public static function getTwElementPane()
    {
        return array(
            'classes' => 'control-group',
            'inner' => array(
                array(
                    'tag' => '',
                    'var' => function($p) {
                        $element = $p->getVars()->element;
                        if (! $element->getLabel()) {
                            return '';
                        }
                        return $p->getView()->formLabel($element);
                    },
                ),
                array(
                    'classes' => 'controls',
                    'var' => function ($p) {
                        $element = $p->getVars()->element;
                        $res = $p->getView()->formElement($element);
                        if ($messages = $element->getMessages()) {
                            foreach ($messages as $message) {
                                $res .= "\n" . sprintf('<span class="help-inline">%s</span>', $message);
                            }
                        }
                        return $res;
                    },
                ),
            ),
        );
    }

    public static function getTwElementResultPane()
    {
        return array(
            'classes' => 'control-group',
            'inner' => array(
                array(
                    'tag' => '',
                    'var' => function($p) {
                        $element = $p->getVars()->element;
                        if (! $element->getLabel()) {
                            return '';
                        }
                        return $p->getView()->formLabel($element);
                    },
                ),
                array(
                    'classes' => 'controls',
                    'var' => function ($p) {
                        $element = $p->getVars()->element;
                        if (! $element->getLabel()) {
                            return '';
                        }
                        return '<label class="control-label" for="inputPassword">' .
                                //$p->getView()->escapeHTML($element->getValue()) .
                                $p->getView()->formPreview($element);
                                '</label>';
                    },
                ),
            ),
        );
    }
}