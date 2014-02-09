<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\ManagerListener;

use Zend\EventManager\AbstractListenerAggregate;

/**
 * Description of ConfigFileListener
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ConfigFileListener extends AbstractListenerAggregate
{
    use ConfigFileTrait;
}
