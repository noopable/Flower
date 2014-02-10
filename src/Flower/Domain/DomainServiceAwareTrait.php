<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

/**
 * Description of DomainServiceAwareTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait DomainServiceAwareTrait
{
    protected $domainService;

    public function getDomainService()
    {
        return $this->domainService;
    }

    public function setDomainService(Service $domainService)
    {
        $this->domainService = $domainService;
    }

}
