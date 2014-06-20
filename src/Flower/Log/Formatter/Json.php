<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Log\Formatter;

use Zend\Json\Json as ZendJson;
use Zend\Log\Formatter\Base;

/**
 * Description of Json
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Json extends Base
{
    /**
     * Formats data to be written by the writer.
     *
     * @param array $event event data
     * @return array
     */
    public function format($event)
    {
        $extra = $event['extra'];
        $event = parent::format($event);
        $event['extra'] = $extra;
        $jsonEncorder = new ZendJson;
        return $jsonEncorder->encode($event);
    }
}
