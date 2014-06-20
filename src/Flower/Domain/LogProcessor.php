<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

use Zend\Log\Processor\ProcessorInterface;

/**
 * Description of LogProcessor
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class LogProcessor implements DomainAwareInterface, ProcessorInterface
{
    use DomainAwareTrait;

    public function process(array $event)
    {
        $domain = $this->getDomain();
        $event['domain_id'] = $event['extra']['domain_id'] = $domain->getDomainId();
        $event['domain_name'] = $event['extra']['domain_name'] = $domain->getDomainName();
        return $event;
    }

}
