<?php
namespace Flower\Form\View;
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

class FormPreview extends BaseAbstractHelper
{
    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|FormElement
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render an element
     *
     * Introspects the element type and attributes to determine which
     * helper to utilize when rendering.
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        switch (true) {
            case ($element instanceof Element\Button):
                $helper = $renderer->plugin('form_button');
                return $helper($element);
            case ($element instanceof Element\Captcha):
                $helper = $renderer->plugin('form_captcha');
                return $helper($element);
            case ($element instanceof Element\Csrf):
                $helper = $renderer->plugin('form_hidden');
                return $helper($element);
            case ($element instanceof Element\Collection):
                $helper = $renderer->plugin('form_collection');
                return $helper($element);
            case ($element instanceof Element\DateTimeSelect):
                $helper = $renderer->plugin('form_date_time_select');
                return $helper($element) . 'TODO: format it';
            case ($element instanceof Element\DateSelect):
                $helper = $renderer->plugin('form_date_select'). 'TODO: format it';
                return $helper($element);
            case ($element instanceof Element\MonthSelect):
                $helper = $renderer->plugin('form_month_select');
                return $helper($element). 'TODO: format it';
        }

        $type = $element->getAttribute('type');
        $htmlEscaper = $renderer->plugin('escape_html');
        $attrEscaper = $renderer->plugin('escape_html_attr');
        switch($type) {
            default:
            case 'email':
            case 'number':
            case 'tel':
            case 'text':
            case 'url':
            case 'checkbox':
                return $htmlEscaper($element->getValue());
            case 'textarea':
                return nl2br($htmlEscaper($element->getValue()));
            case 'radio':
            case 'select':
                $valueOptions = $element->getValueOptions();
                if (isset($valueOptions[$element->getValue()])) {
                    $value = $valueOptions[$element->getValue()];
                } else {
                    $value = $element->getEmptyOption();
                }
                return $htmlEscaper($value);
            case 'multi_checkbox':
                $valueOptions = $element->getValueOptions();
                if (isset($valueOptions[$element->getValue()])) {
                    $value = $valueOptions[$element->getValue()];
                } else {
                    $value = $element->getEmptyOption();
                }
                //TODO: 値候補からリスト化して並べて表示?
                return $htmlEscaper($value);
            case "color":
                //カラーコードをdivボックスで表示？
                return '<div style="backgroud-color:' . $attrEscaper($element->getValue()) . '">'
                    . '&nbsp;' . $htmlEscaper($element->getValue()) . '&nbsp;</div>';
            case "date":
                $helper = $renderer->plugin('form_date');
                return $helper($element). 'TODO: format it';
            case 'datetime':
                $helper = $renderer->plugin('form_date_time');
                return $helper($element). 'TODO: format it';
            case 'datetime-local':
                $helper = $renderer->plugin('form_date_time_local');
                return $helper($element);
            case 'file':
                return $htmlEscaper($element->getValue()) . 'TODO: extension check and image or preview image';
            case 'hidden':
                $helper = $renderer->plugin('form_hidden');
                return $helper($element);
            case 'image':
                $helper = $renderer->plugin('form_image');
                return $helper($element);
            case 'password':
                $helper = $renderer->plugin('form_password');
                return $helper($element);
            case 'month':
                $helper = $renderer->plugin('form_month');
                return $helper($element);
            case 'range':
                $helper = $renderer->plugin('form_range');
                return $helper($element);
            case 'reset':
                //preview disable reset
                return '';
            case 'search':
                $helper = $renderer->plugin('form_search');
                return $helper($element);
            case 'submit':
                $helper = $renderer->plugin('form_submit');
                return $helper($element);
            case 'time':
                $helper = $renderer->plugin('form_time');
                return $helper($element);
            case 'week':
                $helper = $renderer->plugin('form_week');
                return $helper($element);
        }

    }
}
