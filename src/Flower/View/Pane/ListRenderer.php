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
        echo "\n<!-- begin ListRenderer -->\n";
        echo parent::getInnerIterator()->containerBegin($this->getDepth());
    }

    public function endIteration()
    {
        echo parent::getInnerIterator()->containerEnd($this->getDepth());
        echo "\n<!-- end ListRenderer -->\n";
    }

    public function beginChildren()
    {
        $depth = $this->getDepth();
        $indent = str_repeat($this->_indent, $depth);
        $pane = parent::getInnerIterator();
        $pane->setPaneRenderer($this);
        echo $indent . $pane->containerBegin($depth);
        $this->endTagStack[] = $indent . $pane->containerEnd($depth) . "\n";
    }

    public function endChildren()
    {
        echo array_pop($this->endTagStack);
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
        $depth = $this->getDepth();
        $indent = str_repeat($this->_indent, $depth + 1);

        echo $indent . "<!-- start content ListPane -->\n";
        echo $indent . $listPane->wrapBegin($depth);
        echo $indent . $listPane->begin($depth);
        echo $listPane->render($this);
        echo $indent . $listPane->end($depth);
        echo "\n";
        echo $indent . $listPane->wrapEnd($depth);
        echo "\n" . $indent . "<!-- end content ListPane -->\n";
        return $listPane;
    }
}
