<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

/**
 * Compare to PaneRenderer
 *  current calls callback only
 *  beginChildren endChildren calls container(Begin|End)
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ListRenderer extends PaneRenderer
{

    public function beginIteration()
    {
        $this->commentEnable and print("<!-- begin ListRenderer -->" . $this->linefeed);
        echo parent::getInnerIterator()->containerBegin($this->getDepth()) . $this->linefeed;
    }

    public function endIteration()
    {
        echo parent::getInnerIterator()->containerEnd($this->getDepth()) . $this->linefeed;
        $this->commentEnable and print( "<!-- end ListRenderer -->" . $this->linefeed);
    }

    public function beginChildren()
    {
        $depth = $this->getDepth();
        $indent = str_repeat($this->indent, $depth);
        $pane = parent::getInnerIterator();
        $pane->setPaneRenderer($this);
        echo $indent . $pane->containerBegin($depth) . $this->linefeed;
        $this->endTagStack[] = $indent . $pane->containerEnd($depth);
    }

    public function endChildren()
    {
        echo array_pop($this->endTagStack) . $this->linefeed;
    }

    public function current()
    {
        if (!parent::valid()) {
            return;
        }

        $listPane = parent::getInnerIterator()->current();
        if (! $listPane instanceof ListPane) { // check interface ?
            return parent::current();
        }
        $listPane->setPaneRenderer($this);
        $depth = $this->getDepth();
        $indent = str_repeat($this->indent, $depth + 1);

        $this->commentEnable and print($indent . "<!-- start content ListPane -->" . $this->linefeed);
        echo $indent . $listPane->wrapBegin($depth) . $this->linefeed;
        echo $indent . $listPane->begin($depth) . $this->linefeed;
        echo $listPane->render($this) . $this->linefeed;
        echo $indent . $listPane->end($depth) . $this->linefeed;
        echo $indent . $listPane->wrapEnd($depth) . $this->linefeed;
        $this->commentEnable and print($indent . "<!-- end content ListPane -->" . $this->linefeed);
        return $listPane;
    }
}
