<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Service;

use Zend\Cache\Storage\StorageInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Serializer\Adapter\AdapterInterface;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface CacheListenerInterface extends ListenerAggregateInterface
{
    public function setStorage(StorageInterface $storage);

    public function getStorage();

    public function setStorageOptions(array $options);

    public function setSerializer(AdapterInterface $serializer);

    public function getSerializer();
}
