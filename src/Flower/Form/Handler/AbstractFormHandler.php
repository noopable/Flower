<?php
namespace Flower\Form\Handler;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\FilePostRedirectGet\Plugin\FilePostRedirectGet;

use Zend\Form\FormInterface;
use Zend\Form\FormElementManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractPluginManager;

/**
 * Description of CreateProductsHandler
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
abstract class AbstractFormHandler implements FormHandlerInterface, DelegateHandlerInterface
{
    use ServiceLocatorAwareTrait;

    protected $name;

    protected $parentName;

    protected $form;

    protected $post;

    protected $prg;

    protected $object;

    protected $options;

    protected $salt;

    protected $handlerPluginManager;

    protected $formElementManager;

    public function __construct($options = array())
    {
        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'name':
                    $this->setName($value);
                    break;
                case 'parent':
                    $this->delegate($value);
                    break;
                case 'servicelocator':
                    $this->setServiceLocator($value);
                    break;
                case 'post':
                    $this->setPost($value);
                    break;
                case 'prg':
                    $this->setPrg($value);
                    break;
                case 'form':
                    $this->setForm($value);
                    break;
                case 'object':
                    $this->setObject($value);
                    break;
                case "token":
                    $this->setToken($value);
                    break;
                default:
                    $this->options[$key] = $value;
                    break;
            }
        }
        if (!isset($this->name)) {
            $this->name = get_class($this);
        }
    }

    abstract public function init();

    abstract public function handle();

    abstract public function processRequest();

    abstract public function prepareForm();

    /**
     *
     * @return FormElementManager
     */
    public function getFormElementManager()
    {
        if (!isset($this->formElementManager)) {
            $serviceLocator = $this->getServiceLocator();
            if ($serviceLocator instanceof AbstractPluginManager) {
                $serviceLocator = $serviceLocator->getServiceLocator();
            }
            if (($serviceLocator instanceof ServiceLocatorInterface)
                && ($serviceLocator->has('FormElementManager'))) {
                $this->formElementManager = $serviceLocator->get('FormElementManager');
            } else {
                $this->formElementManager = new FormElementManager;
                $this->formElementManager->setServiceLocator($serviceLocator);
            }
        }
        return $this->formElementManager;
    }

    /**
     *
     * @return FormElementManager
     */
    public function getHandlerPluginManager()
    {
        if (!isset($this->handlerPluginManager)) {
            $serviceLocator = $this->getServiceLocator();
            if ($serviceLocator instanceof AbstractPluginManager) {
                $serviceLocator = $serviceLocator->getServiceLocator();
            }
            if ($serviceLocator instanceof ServiceLocatorInterface) {
                if ($serviceLocator->has('HandlerPluginManager')) {
                    $this->handlerPluginManager = $serviceLocator->get('HandlerPluginManager');
                }
            }

            if (!isset($this->handlerPluginManager)) {
                $this->handlerPluginManager = new HandlerPluginManager;

            }
            if (isset($this->handlerPluginNamespaces)) {
                $this->handlerPluginManager->setNamespaces($this->handlerPluginNamespaces);
            } else {
                $this->handlerPluginManager->addNamespace(__NAMESPACE__);
            }
        }
        return $this->handlerPluginManager;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName($fullName = false)
    {
        if ($fullName && isset($this->parentName)) {
            return $this->parentName . '[' . $this->name . ']';
        }
        return $this->name;
    }

    public function canDelegate($name)
    {
        return $this->getHandlerPluginManager()->has($name);
    }

    public function createDelegater($name)
    {
        $handler = $this->getHandlerPluginManager()->get($name);
        if ($handler instanceof DelegateHandlerInterface) {
            $handler->delegate($this);
            return $handler;
        }
        return null;
    }

    public function delegate(FormHandlerInterface $formHandler)
    {
        $this->parentName = $formHandler->getName(true);
        $this->setPost($formHandler->getPost());
        $this->setForm($formHandler->getForm());
        $this->setObject($formHandler->getObject());
        if ($formHandler instanceof ServiceLocatorAwareInterface) {
            $this->setServiceLocator($formHandler->getServiceLocator());
        }
        return $this;
        // setOptions is not interface method
    }

    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }

    public function getForm()
    {
        $this->init();
        return $this->form;
    }
    /**
     *
     * @param array|false $post
     */
    public function setPost($post)
    {
        $this->post = $post;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function getOption($name, $default = null)
    {
        if (! isset($this->options[$name])) {
            return $default;
        }
        return $this->options[$name];
    }

    /**
     *
     * @param FilePostRedirectGet $prg
     */
    public function setPrg(FilePostRedirectGet $prg)
    {
        $this->prg = $prg;
    }

    public function getPrg()
    {
        return $this->prg;
    }
}
