<?php
namespace Flower\File\Adapter;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\File\Event;
/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface AdapterInterface
{
    public function configure();
    
    public function onRead(Event $event);
    
    public function onWrite(Event $event);
}
