<?php
namespace Flower\FilePostRedirectGet\Element;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\FilePostRedirectGet\Plugin\FilePostRedirectGet as Plugin;

use Zend\Form\Element;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputProviderInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;


class File extends Element implements
    InputProviderInterface,
    ElementPrepareAwareInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    /**
     * Seed attributes
     *
     * @var array
     */
    protected $attributes = array(
        'type' => 'file',
    );

    /**
     *
     * @var Plugin|false
     */
    protected $fprg;

    /**
     * Prepare the form element (mostly used for rendering purposes)
     *
     * @param  FormInterface $form
     * @return mixed
     */
    public function prepareElement(FormInterface $form)
    {
        // Ensure the form is using correct enctype
        $form->setAttribute('enctype', 'multipart/form-data');
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        $fprg = $this->getFPRG();
        return array(
            'name'     => $this->getName(),
            'required' => false,
            'filters' => array(
                function($value) use ($fprg) {
                    if (is_string($value)) {
                        return $fprg->getFile($value);
                    } else {
                        return $value;
                    }
                },
            ),
            'validators' => array(
                $fprg,
            ),
        );
    }

    public function setFPRG(Plugin $fprg)
    {
        $this->fprg = $fprg;
    }

    public function getFPRG()
    {
        if (isset($this->fprg)) {
            return $this->fprg;
        }

        $serviceLocator = $this->getServiceLocator();
        if (!$serviceLocator) {
            throw new \RuntimeException(__CLASS__ . ' depends on the ServiceLocator');
        }

        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        if (!$serviceLocator instanceof ServiceLocatorInterface) {
            throw new \RuntimeException('ServiceLocator not found . Please make the form or elements by $sl->get("FormElementManager");');
        }

        if ($serviceLocator->has('Flower\FormPostRedirectGet\Plugin\FilePostRedirectGet')) {
            $this->fprg = $serviceLocator->get('Flower\FormPostRedirectGet\Plugin\FilePostRedirectGet');
        } else {
            $this->fprg = false;
        }

        return $this->fprg;
    }

    public function getPreviewValue()
    {
        $fprg = $this->getFPRG();
        $route = $fprg->getPreviewRoute();
        $params = $fprg->getPreviewParams();
        $value = $this->getValue();
        if (!is_array($value)) {
            return array();
        }
        if (!ArrayUtils::isList($value)) {
            $value = array($value);
        }
        $res = array();
        foreach ($value as $file) {
            $token = $file['token'];
            $fprgKeyName = $fprg->getRequestKey();
            $params[$fprgKeyName] = $token;
            $res[] = array('route' => $route, 'params' => $params, 'file' => $file);
        }
        return $res;

    }

    public function getTokens()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            return array();
        }

        if (! ArrayUtils::isList($value)) {
            $value = array($value);
        }
        $tokens = array();
        foreach ($value as $file) {
            $tokens[] = $file['token'];
        }
        return $tokens;
    }

    public function getPreviewTemplate()
    {
        return $this->getOption('preview-template', false);
    }
}
