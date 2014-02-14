<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\PaneClass;

use Flower\View\Pane\Exception\RuntimeException;
use Flower\View\Pane\PaneRenderer;

/**
 * Description of EntityScriptPane
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class EntityScriptPane extends ViewScriptPane implements EntityAwareInterface
{
    use EntityAwareTrait;

    protected $entityKeyInView = 'entity';

    public function _render(PaneRenderer $paneRenderer)
    {
        $view = $paneRenderer->getView();
        if (!$view) {
            throw new RuntimeException('paneRenderer has no PhpRenderer');
        }
        $view->{$this->entityKeyInView} = $this->getEntity();
        return parent::_render($paneRenderer);
    }
}
