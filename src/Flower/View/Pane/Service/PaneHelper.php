<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Flower\View\Pane\Builder\Builder;
use Flower\View\Pane\PaneClass\PaneInterface;
use Zend\View\Helper\AbstractHelper;
use RecursiveIterator;

/**
 * ペインヘルパーを使う使いどころとしては、たとえばコントローラーでペイン構造を定義して
 * それをViewModelにセットすれば、ペイン構造を使ったビューが簡単に作れるということだ。
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 *
 */
class PaneHelper extends AbstractHelper
{
    /**
     * Variable to which object will be assigned
     * @var string
     */
    public $objectKey = 'pane';

    public $defaultPane = array('tag' => '','inner' => array('classes' => 'container'));

    public $defaultContent = 'content';

    public $defaultPaneClass = 'Flower\View\Pane\PaneClass\Pane';

    public $paneRenderer = 'Flower\View\Pane\PaneRenderer';

    protected $builder;

    public function abstractFactoryPaneRenderer(RecursiveIterator $pane)
    {
        $renderer = new $this->paneRenderer($pane);
        $renderer->setVars($this->getView()->vars());
        $renderer->setView($this->getView());
        return $renderer;
    }

    public function paneFactory(array $array)
    {
        return $this->getBuilder()->build($array);
    }

    /**
     * Renders a template fragment within a variable scope distinct from the
     * calling View object.
     *
     * If no arguments are passed, returns the helper instance.
     *
     * If the $model is an array, it is passed to the view object's assign()
     * method.
     *
     * If the $model is an object, it first checks to see if the object
     * implements a 'toArray' method; if so, it passes the result of that
     * method to to the view object's assign() method. Otherwise, the result of
     * get_object_vars() is passed.
     *
     * @param  string $name Name of view script
     * @param  array $model Variables to populate in the view
     * @return string|Partial
     * @throws Exception\RuntimeException
     */
    public function __invoke($pane = null, $default = null)
    {
        if (null === $pane) {
            $pane = $this->getView()->get($this->objectKey);
            if (! $pane) {
                $pane = $this->paneFactory($this->defaultPane);
            }
        }

        if (is_array($pane)) {
            $pane = $this->paneFactory($pane);
        }

        if (!$pane instanceof PaneInterface) {
            if (null === $default) {
                $default = $this->getView()->get($this->defaultContent);
            }
            return '<!-- pane not found. use default -->' . $default ;
        }
        //Paneレンダラは複数使うこともあるので、基本的にコールされるたびに育成される。
        $renderer = $this->abstractFactoryPaneRenderer($pane);

        return $renderer;
    }

    /**
     * Set object key
     *
     * @param  string $key
     * @return Partial
     */
    public function setObjectKey($key)
    {
        if (null === $key) {
            $this->objectKey = null;
        } else {
            $this->objectKey = (string) $key;
        }

        return $this;
    }

    /**
     * Retrieve object key
     *
     * The objectKey is the variable to which an object in the iterator will be
     * assigned.
     *
     * @return null|string
     */
    public function getObjectKey()
    {
        return $this->objectKey;
    }

    public function setBuilder(Builder $builder = null)
    {
        if (null === $builder) {
            $builder = new Builder(array('pane_class' => $this->defaultPaneClass));
        }
        $this->builder = $builder;
    }

    public function getBuilder()
    {
        if (!isset($this->builder)) {
            $this->setBuilder();
        }
        return $this->builder;
    }
}
