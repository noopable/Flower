<?php
namespace Flower\Form;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

class FormUtilsGumby
{

    public static function getGumbyErrorElementPane()
    {
        $pane = self::getGumbyElementPane();
        $pane['classes'] = array('alert', 'danger');
        return $pane;
    }

    public static function getGumbyElementPane()
    {
        return array(
            'classes' => array('form-row', 'row'),
            'inner' => array(
                array(
                    'classes' => array('form-label'),
                    'var' => function($p) {
                        $element = $p->getVars()->element;
                        if (! $element->getLabel()) {
                            return '';
                        }
                        return $p->getView()->formLabel($element);
                    },
                ),
                array(
                    'classes' => array('field'),
                    'var' => function ($p) {
                        $element = $p->getVars()->element;
                        $res = $p->getView()->formElement($element);
                        if ($messages = $element->getMessages()) {
                            foreach ($messages as $message) {
                                $res .= "\n" . sprintf('<span class="warning alert">%s</span>', $message);
                            }
                        }
                        return $res;
                    },
                ),
            ),
        );
    }

    public static function getGumbyElementResultPane()
    {
        return array(
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
                        return '<label for="inputPassword">' .
                                //$p->getView()->escapeHTML($element->getValue()) .
                                $p->getView()->formPreview($element);
                                '</label>';
                    },
                ),
            ),
        );
    }
}