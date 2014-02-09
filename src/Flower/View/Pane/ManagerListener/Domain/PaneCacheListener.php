<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\ManagerListener\Domain;

use Flower\View\Pane\ManagerListener\AbstractLazyLoadCacheListener;
use Flower\View\Pane\ManagerListener\PaneCacheTrait;
use Zend\Serializer\Adapter\PhpSerialize;
use Zend\Serializer\Adapter\AdapterInterface;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class PaneCacheListener extends AbstractLazyLoadCacheListener
{
    use PaneCacheTrait;
    /**
     *
     * @return AdapterInterface
     */
    public function getSerializer()
    {
        if (!isset($this->serializer)) {
            $this->serializer = new PhpSerialize;
        }
        return $this->serializer;
    }

}
