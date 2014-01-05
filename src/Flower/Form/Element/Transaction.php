<?php
namespace Flower\Form\Element;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Form\Element\Hidden;
use Zend\Form\ElementPrepareAwareInterface;
use Zend\Form\FormInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\Session\Container as SessionContainer;

/**
 * Description of Name
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Transaction extends Hidden implements ElementPrepareAwareInterface, ServiceLocatorAwareInterface
{

    protected $serviceLocator;

    protected $maxHistory = 5;

    protected $formElementManager;

    protected $session;

    protected $timeout = 7200;

    protected $state;

    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name = null, $options = array())
    {
        if (null === $name) {
            $name = '__trans_id';
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'session':
                    $this->setSession($value);
                    unset($options[$key]);
                    break;
                case 'timeout':
                    $this->setTimeout($value);
                    unset($options[$key]);
                    break;
                case 'state':
                    $this->setState($value);
                    unset($options[$key]);
                default:
                    // ignore unknown options
                    break;
            }
        }

        parent::__construct($name, $options);

    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $this->formElementManager = $serviceLocator;
            $this->serviceLocator = $this->formElementManager->getServiceLocator();
        } else {
            $this->serviceLocator = $serviceLocator;
        }
        return $this;
    }

    public function getServiceLocator()
    {
        if (! isset($this->serviceLocator)) {
            throw new \RuntimeException(__CLASS__ . ' needs some components, please set serviceLocator before init()');
        }
        return $this->serviceLocator;
    }

    public function retrieveFormData()
    {
        $key = $this->getValue();
        if (!$key) {
            return array();
        }
        $sessionData = $this->getSession()->data;
        if (!is_array($sessionData)) {
            //throw session expired exception?
            //throw data is already removed?
            return array();
        }
        $data = end($sessionData);
        if (is_array($data)) {
            return current($data);
        } else {
            return array();
        }
    }

    public function getSessionState()
    {
        $key = $this->getValue();
        if (!$key) {
            return array();
        }
        $sessionData = $this->getSession()->data;
        if (!is_array($sessionData)) {
            //throw session expired exception?
            //throw data is already removed?
            return array();
        }
        $data = end($sessionData);
        if (is_array($data)) {
            return key($data);
        } else {
            return 0;
        }
    }

    public function getState()
    {
        if (!isset($this->state)) {
            $this->state = $this->getSessionState();
        }
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getRawData()
    {
        return $this->getSession()->data;
    }

    public function resetData()
    {
        $this->getSession()->data = array();
    }

    public function prepareElement(FormInterface $form)
    {
        if ($form->hasValidated()) {
            $this->pushData($form->getData(), $this->state);
        }
    }

    public function pushData(array $data, $state = 0)
    {
        asort($data);
        if (! is_string($state) && !is_int($state)) {
            $state = 0;
        }

        $data = array($state => $data);

        $session = $this->getSession();
        $sessionData = $session->data;
        $sessionData[] = $data;

        if (count($sessionData) > $this->maxHistory) {
            array_splice($sessionData, 0, count($sessionData) - $this->maxHistory);
        }

        $session->data = $sessionData;
        return $this;
    }

    public function setSession(SessionContainer $session = null)
    {
        if (null === $session) {
            $session = new SessionContainer($this->getSessionName());
        } else {
            $this->setValue($session->getName());
        }

        $session->setExpirationSeconds($this->getTimeout());

        $this->session = $session;
        if (!isset($this->session->data)) {
            $this->session->data = array();
        }

        if (!isset($this->session->properties)) {
            $this->session->properties = array();
        }
    }

    public function getSession()
    {
        if (!isset($this->session)) {
            // Using fully qualified name, to ensure polyfill class alias is used
            $this->setSession();
        }
        return $this->session;
    }

    /**
     * Get session namespace for CSRF token
     *
     * Generates a session namespace based on salt, element name, and class.
     *
     * @return string
     */
    public function getSessionName()
    {
        if (!isset($this->value) || empty($this->value)) {
            $this->setValue($this->generateSessionName());
        }
        return $this->getValue();
    }

    public function generateSessionName()
    {
        $name = uniqid($this->getSessionPrefix());
        return $name;
    }

    protected function getSessionPrefix()
    {
        return str_replace('\\', '_', $this->getName());
    }

        /**
     * Set the element value
     *
     * @param  mixed $value
     * @return Element
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     *
     *
     * @param  int|null $ttl
     * @return Csrf
     */
    public function setTimeout($ttl)
    {
        $this->timeout = ($ttl !== null) ? (int) $ttl : null;
        return $this;
    }

    /**
     *
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setProperty($name, $value)
    {
        $properties = $this->getSession()->properties;
        $properties[$name] = $value;
        $this->getSession()->properties = $properties;
    }

    public function getProperty($name, $default = null)
    {
        $properties = $this->getSession()->properties;
        if (! isset($properties[$name])) {
            return $default;
        }
        return $properties[$name];
    }
}
