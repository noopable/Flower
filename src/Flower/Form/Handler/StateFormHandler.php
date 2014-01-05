<?php
namespace Flower\Form\Handler;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Form\FormInterface;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
/**
 * Description of CreateProductsHandler
 * Route内でsaltを持ちまわっていただく必要がある。
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class StateFormHandler extends AbstractSessionFormHandler
{

    protected $init;

    protected $fieldsetsDef;

    /**
     *
     * @var array key is nameOrClass value is class
     */
    protected $elementInvokables;

    /**
     * 汎用的なクラスに書き換えておく
     *
     * @var string button fieldset class
     */
    protected $buttonsInvokableClass = 'Flower\Form\Handler\Fieldset\ButtonGroupFieldset';

    /**
     *
     * @var string
     */
    protected $formClass;

    /**
     * 状態を管理する配列
     *
     * @var array
     */
    protected $states;

    public function init()
    {
        if ($this->init) {
            return;
        }
        $this->init = true;

        reset($this->states);
        if (isset($this->states) && is_array($this->states)) {
            $this->initState = key($this->states);
        }

        $formElementManager = $this->getFormElementManager();
        if (!$formElementManager->has('buttons', false, false) && isset($this->buttonsInvokableClass)) {
            $formElementManager->setInvokableClass('buttons', $this->buttonsInvokableClass);
        }
        if ($this->elementInvokables) {
            foreach($this->elementInvokables as $name => $class) {
                $formElementManager->setInvokableClass($name, $class);
            }
        }
        if (!isset($this->form)) {
            $formName = $this->formClass ?: 'form';
            $this->form = $formElementManager->get($formName);
        }
        $this->injectFieldsets($this->getProperty('fieldsets'), $this->form);
    }

    /**
     * simple implement next prev state form
     *
     * @return string
     */
    public function processRequest()
    {
        $state = parent::processRequest();
        if (! is_array($this->states)) {
            return $state;
        }

        if (!isset($this->states[$state])) {
            return $state;
        }

        if (!$this->post) {
            return $state;
        }
        //process buttons
        if (!$this->post['buttons']) {
            return $state;
        }

        //check prev current next states
        $aState = $this->getParsedState($state);

        if (isset($aState['form_options'])) {
            $this->form->setOptions($aState['form_options']);
        }
        if (isset($this->post['buttons']['submit_next'])) {
            if (isset($aState['fieldsets'])) {
                $validationGroup = array();
                foreach ($aState['fieldsets'] as $fieldset) {
                    if ($this->form->has($fieldset)) {
                        $validationGroup[] = $fieldset;
                    }
                }
                if (count($validationGroup)) {
                    $this->form->setValidationGroup($validationGroup);
                }
            }
            if (! $this->form->isValid()) {
                return $state;
            }
            if (is_string($aState['nextKey'])) {
                $state = $aState['nextKey'];
            } elseif(is_callable($aState['nextKey'])) {
                $res = $aState['nextKey']($this);
                $state = is_string($res) ? $res : $state;
            }
        } elseif(isset($this->post['buttons']['submit_back'])) {
            if (is_string($aState['prevKey'])) {
                $state = $aState['prevKey'];
            } elseif (is_callable($aState['prevKey'])) {
                $res = $aState['prevKey']($this);
                $state = is_string($res) ? $res : $state;
            }
        }

        return $state;
    }

    public function prepareFieldsets($sets = array(), FormInterface $form = null)
    {
        if (null === $form) {
            $form = $this->form;
        }
        $fieldsets = $form->getFieldsets();
        $names = array();

        foreach ($fieldsets as $fieldset) {
            $names[] = $fieldset->getName();
        }
        foreach (array_diff($names, $sets) as $name) {
            $form->remove($name);
        }

        $forInject = array();
        foreach (array_diff($sets, $names) as $name) {
            //initialize for foreach
            $def = $this->getFieldsetDef($name);
            $forInject[] = $def;
            $this->saveFieldset($name, $def);
        }
        if (count($forInject)) {
            $this->injectFieldsets($forInject, $form);
        }

    }

    public function addButtons($options = array())
    {
        if (!isset($options['buttons'])) {
            $options['buttons'] = $options;
        }
        $buttons = $this->getFormElementManager()->get('buttons', $options);
        $this->form->add($buttons);
    }

    public function getFieldsetDef($name)
    {
        //CMS化するときは、Di化した方がいいよね。
        if (!isset($this->fieldsetsDef) || !isset($this->fieldsetsDef[$name])) {
            throw new \RuntimeException('Please set your definition of fieldsets as ' . $name);
        }
        return $this->fieldsetsDef[$name];
    }

    public function injectFieldsets(array $fieldsetDefs = null, FormInterface $form = null)
    {
        if (null === $fieldsetDefs) {
            return;
        }

        $formElementManager = $this->getFormElementManager();
        if (null === $form) {
            $form = $this->form;
        }
        foreach ($fieldsetDefs as $def) {
            if (!isset($def['name'])) {
                continue;
            }
            $name = $def['name'];
            $invokaleExists = $formElementManager->has($name, false, false);
            if (isset($def['class'])) {
                $class = $def['class'];
            } else {

                if (!$invokaleExists) {
                    if (class_exists($name)) {
                        $class = $name;
                    } else {
                        continue;
                    }
                }
            }
            if (!$invokaleExists) {
                //creationOptionsをAbstractFactoriesが受け入れてくれればこの必要はない。
                $formElementManager->setInvokableClass($name, $class);
            }
            $options = isset($def['options']) ? $def['options'] : array();
            $fieldset = $formElementManager->get($name, $options);
            $fieldset->setName($name);
            $form->add($fieldset);
        }
    }

    public function saveFieldset($name, $def)
    {
        $fieldsets = $this->getProperty('fieldsets', array());
        if (!is_array($fieldsets)) {
            throw \RuntimeException('saved session property fieldsets is not array');
        }
        if (!isset($fieldsets[$name])) {
            $fieldsets[$name] = array();
        }
        if (!is_array($fieldsets[$name])) {
            throw \RuntimeException("saved session property fieldsets[$name] is not array");
        }
        $fieldsets[$name]['name'] = $name;
        if (isset($def['class'])) {
            $fieldsets[$name]['class'] = $def['class'];
        }
        if (isset($def['options'])) {
            $fieldsets[$name]['options'] = $def['options'];
        }
        $this->setProperty('fieldsets', $fieldsets);
    }

    public function removeFieldset($name)
    {
        $fieldsets = $this->getProperty('fieldsets', array());
        if (!is_array($fieldsets)) {
            throw \RuntimeException('saved session property fieldsets is not array');
        }
        if (!isset($fieldsets[$name])) {
            return;
        }
        unset($fieldsets[$name]);
        if ($this->form->has($name)) {
            $this->form->remove($name);
        }
        $this->setProperty('fieldsets', $fieldsets);
    }

    public function getParsedState($state)
    {
        if (!isset($this->states[$state])) {
            return null;
        }

        $res = $this->states[$state];
        if (!isset($res['prevKey']) || !isset($res['nextKey'])) {
            //process auto detect
            $iterator = new \ArrayIterator($this->states);
            $prevKey = $iterator->key();
            do {
                if ($iterator->key() === $state) {
                    $nextKey = $iterator->key();
                    $iterator->next();
                    if ($iterator->valid()) {
                        $nextKey = $iterator->key();
                    }
                    break;
                } else {
                    $prevKey = $iterator->key();
                }
                $iterator->next();
            } while ($iterator->valid());
            if (!isset($res['prevKey'])) {
                $res['prevKey'] = $prevKey;
            }
            if (!isset($res['nextKey'])) {
                $res['nextKey'] = $nextKey;
            }
        }
        return $res;
    }

    public function prepareForm()
    {
        $state = $this->getState();
        $form = $this->form;
        if (!isset($this->states[$state])) {
            return $form;
        }

        $aState = $this->states[$state];
        if (isset($aState['form_options'])) {
            $form->setOptions($aState['form_options']);
        }
        if (isset($aState['fieldsets'])) {
            $this->prepareFieldsets($aState['fieldsets']);
        }
        if (isset($aState['buttons'])) {
            $this->addButtons($aState['buttons']);
        }
        return $form;
    }
}
