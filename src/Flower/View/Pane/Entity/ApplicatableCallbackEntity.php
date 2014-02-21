<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Entity;

use Flower\View\Pane\PaneClass\PaneInterface;

/**
 * Description of ArrayApplicatableEntity
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ApplicatableCallbackEntity implements ApplicatePaneInterface
{
    protected $params;

    protected $callback;

    public function __construct($callback, array $params = array())
    {
        $this->callback = $callback;

        $this->params = $params;
    }

    public function apply(PaneInterface $pane)
    {
        if (is_callable($this->callback)) {
            return call_user_func($this->callback, $pane, $this->params);
        }
    }

}
