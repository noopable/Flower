<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

/**
 * Description of PaneWrapMethodsTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait PaneWrapMethodsTrait
{
    /**
     * wrap tags
     *
     */
    /**
     *
     * @var type
     */
    protected $containerBegin;

    protected $containerEnd;

    protected $wrapBegin;

    protected $wrapEnd;

    protected $begin;

    protected $end;

    public function containerBegin($depth = null)
    {
        if (!isset($this->containerBegin)) {
            return $this->wrapBegin($depth);
        }
        return $this->containerBegin;
    }

    public function containerEnd($depth = null)
    {
        if (!isset($this->containerEnd)) {
            return $this->wrapEnd($depth);
        }
        return $this->containerEnd;
    }

    public function wrapBegin($depth = null)
    {
        if (! isset($this->wrapBegin)) {
            return $this->begin($depth);
        }
        return $this->wrapBegin;
    }

    public function wrapEnd($depth = null)
    {
        if (! isset($this->wrapEnd)) {
            return $this->end($depth);
        }
        return $this->wrapEnd;
    }

    public function begin($depth = null)
    {
        return $this->begin;
    }

    public function end($depth = null)
    {
        return $this->end;
    }


    public function setBegin($begin)
    {
        $this->begin = $begin;
    }

    public function setEnd($end)
    {
        $this->end = $end;
    }

    public function setWrapBegin($wrapBegin)
    {
        $this->wrapBegin = $wrapBegin;
    }

    public function setWrapEnd($wrapEnd)
    {
        $this->wrapEnd = $wrapEnd;
    }

    public function setContainerBegin($containerBegin)
    {
        $this->containerBegin = $containerBegin;
    }

    public function setContainerEnd($containerEnd)
    {
        $this->containerEnd = $containerEnd;
    }
}
