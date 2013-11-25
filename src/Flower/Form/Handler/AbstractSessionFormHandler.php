<?php
namespace Flower\Form\Handler;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Session\Container as SessionContainer;
use Zend\Form\FormInterface;
use Zend\Stdlib\ArrayUtils;

use RecursiveArrayIterator;
use RecursiveIteratorIterator;

/**
 * Description of SessionFormHandler
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
abstract class AbstractSessionFormHandler extends AbstractFormHandler
{
    protected $maxHistory = 5;

    protected $session;

    protected $timeout = 7200;

    protected $initState = 'init';//root handler

    protected $state;

    protected $token;

    /**
     * @param  null|int|string  $name    Optional name for the element
     * @param  array            $options Optional options for the element
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = array())
    {
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
                case 'token':
                    $this->setToken($value);
                    unset($options[$key]);
                default:
                    // ignore unknown options
                    break;
            }
        }
        if (!isset($options['name'])) {
            $options['name'] = 'session_form_handler';
        }
        parent::__construct($options);

    }

    public function setToken($token = null)
    {
        $token = (string) $token;
        if (empty($token)) {
            $token = $this->newToken();
        }
        $this->token = $token;
    }

    public function newToken()
    {
        //nomalize
        $name = str_replace('\\', '_', $this->getName());
        //for session
        $name = preg_replace('/^[^a-z]/i', 'a', $name);
        $token = uniqid($name);
        return $token;
    }

    public function getToken()
    {
        if (!isset($this->token)) {
            $this->setToken();
        }
        return $this->token;
    }

    public function retrieveFormData()
    {
        $stack = $this->getDataStack();
        if (!is_array($stack) || empty($stack)) {
            //throw session expired exception?
            //throw stack is already removed?
            return array();
        }
        $composed = end($stack);
        if (is_array($composed) && isset($composed['data'])) {
            return $composed['data'];
        } else {
            return array();
        }
    }

    public function getSessionState()
    {
        $stack = $this->getDataStack();
        if (!is_array($stack) || empty($stack)) {
            //throw session expired exception?
            //throw stack is already removed?
            return $this->initState;
        }
        $composed = end($stack);
        if (is_array($composed)) {
            $state = $composed['state'];
            if (!(is_string($state))) {
                throw new \RuntimeException('state must to be string');
            }
            return $state;
        } else {
            return $this->initState;
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

    public function getDataStack()
    {
        if (! $this->getSession()->offsetExists('stack')) {
            return array();
        }
        return $this->getSession()->stack;
    }

    public function resetStack()
    {
        $this->getSession()->stack = array();
    }

    public function save()
    {
        $data = $this->retrieveFormData();
        if ($this->form->hasValidated()) {
            $data = array_merge($data, $this->form->getData());
        }

        $this->pushData($data, $this->state);
    }

    public function handle()
    {
        $this->init();

        $state = $this->processRequest();
        $this->setState($state);
        $this->save();

        $form = $this->prepareForm();
        //表示用フォームを返すことを許可
        if (! $form instanceof FormInterface) {
            $form = $this->form;
            $form->prepare();
        }
        return $form;
    }

    public function processRequest()
    {
        $state = $this->getState();

        if ($state !== $this->name && $this->canDelegate($state)) {
            $handler = $this->createDelegater($state);
            if (!$handler instanceof FormHandlerInterface) {
                throw new \RuntimeException('handler is not formHandler');
            }
            return $handler->processRequest();
        }

        $form = $this->getForm();
        $data = $this->retrieveFormData();
        if (is_array($this->post) && $this->post) {
            $data = ArrayUtils::merge($data, $this->post);
            $data = $this->mergeFiles($data, $this->post);
        }
        if ($data) {
            $form->setData($data);
            $form->isValid();
        }
        return $state;
    }

    /**
     * Merge for files
     *
     * @param  array $a
     * @param  array $b
     * @return array
     */
    protected function mergeFiles(array $data, array $post)
    {
        $name     = array();
        $files = array();
        $rai = new RecursiveArrayIterator($post);
        $rii = new RecursiveIteratorIterator($rai, RecursiveIteratorIterator::SELF_FIRST);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        do {
            if (is_array($rii->current())) {
                $file = $rii->current();
                if (isset($file['tmp_name']) && is_file($file['tmp_name']) && isset($file['ns'])) {
                    $files[] = $file;
                    $ns = $file['ns'];
                    if (is_int(array_pop($ns))) {
                        //multi upload
                        $tracer = &$data;
                        foreach ($name as $index) {
                            if (!isset($tracer[$index])) {
                                break;
                            }
                            $tracer = &$tracer[$index];
                        }
                        $tracer = null;
                    }
                }
            }
            $rii->next();
        } while($rii->valid());

        foreach ($files as $file) {
            if (!isset($file['ns'])) {
                continue;
            }
            $tracer = &$data;
            $name = $file['ns'];
            foreach ($name as $index) {
                if (!isset($tracer[$index])) {
                    $tracer[$index] = array();
                }
                $tracer = &$tracer[$index];
            }
            $tracer = $file;
        }
        return $data;
    }

    public function prepareForm()
    {
        $state = $this->getState();
        if (!($state === $this->initState || $state === $this->name)) {
            $handler = $this->createDelegater($state);
            if ($handler instanceof FormHandlerInterface) {
                $form = $handler->prepareForm();
            }
        }
        if (!isset($form)) {
            $form = $this->form;
        }
        if ($form instanceof FormInterface) {
            $form->prepare();
        }
        return $form;
    }

    public function pushData(array $data, $state = null)
    {
        asort($data);
        if (null === $state) {
            $state = $this->initState;
        }

        $composed = array(
            'data' => $data,
            'state' => $state,
        );
        $session = $this->getSession();
        $stack = $this->getDataStack();
        $stack[] = $composed;

        if (count($stack) > $this->maxHistory) {
            array_splice($stack, 0, count($stack) - $this->maxHistory);
        }

        $session->stack = $stack;
        return $this;
    }

    public function setSession(SessionContainer $session = null)
    {
        if (null === $session) {
            $session = new SessionContainer($this->getToken());
        } else {
            $this->setToken($session->getName());
        }

        $session->setExpirationSeconds($this->getTimeout());

        if (!$session->offsetExists('stack')) {
            $session->stack = array(array(
                'state' => $this->initState,
                'data' => array(),
            ));
        }

        if (!$session->offsetExists('properties')) {
            $session->properties = array();
        }

        $this->session = $session;
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
        $properties = array();
        if ($this->getSession()->offsetExists('properties')) {
            $properties = $this->getSession()->properties;
        }
        $properties[$name] = $value;
        $this->getSession()->properties = $properties;
    }

    public function getProperty($name, $default = null)
    {
        if (!$this->getSession()->offsetExists('properties')) {
            return $default;
        }
        $properties = $this->getSession()->properties;
        if (! isset($properties[$name])) {
            return $default;
        }
        return $properties[$name];
    }
}
