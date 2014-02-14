<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use ArrayObject;
use Flower\View\Pane\PaneRenderer;

/**
 * Description of PaneTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait PaneTrait
{
    use PanePublicTrait, PaneWrapMethodsTrait;

    protected static $defaultFactoryClass = 'Flower\View\Pane\Factory\PaneFactory';

    protected $options;

    protected $paneId;

    protected $paneRenderer;

    protected $registry;

    /**
     * properties for rendering
     */
    /**
     *
     * @var string
     */
    public $indent = "  ";

    /**
     *
     * @var type
     */
    public $linefeed = "\n";

    /**
     *
     * @var boolean
     */
    public $commentEnable = true;

    public function setPaneId($paneId)
    {
        $this->paneId = $paneId;
    }

    public function getPaneId()
    {
        return $this->paneId;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getOption($name)
    {
        if (! isset($this->options[$name])) {
            return;
        }
        return $this->options[$name];
    }

    public function getOptions()
    {
        if (!isset($this->options)) {
            return array();
        }
        return $this->options;
    }

    public function setOption($name, $option)
    {
        if (!isset($this->options)) {
            $this->options = array();
        }
        $this->options[$name] = $option;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public static function getFactoryClass()
    {
        if (isset(static::$factoryClass)) {
            return static::$factoryClass;
        }
        return static::$defaultFactoryClass;
    }

    public function setPaneRenderer(PaneRenderer $paneRenderer)
    {
        $this->indent = $paneRenderer->indent;
        $this->linefeed = $paneRenderer->linefeed;
        $this->commentEnable = $paneRenderer->commentEnable;
        $this->paneRenderer = $paneRenderer;
    }

    public function getPaneRenderer()
    {
        return $this->paneRenderer;
    }

    public function setRegistry($registry)
    {
        $this->registry = $registry;
    }

    public function getRegistry()
    {
        if (!isset($this->registry)) {
            $this->registry = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        }
        return $this->registry;
    }

    public function hasContent()
    {
        //empty() is true when '0'
        return !empty($this->var) || '0' === $this->var;
    }
}
