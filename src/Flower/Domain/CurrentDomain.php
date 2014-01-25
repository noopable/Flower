<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

use Flower\Exception\DomainException;

/**
 * CurrentDomainを利用するには、適切なRouteを作成し、
 * domain_idとdomain_nameをRouteMatchから取得できる
 * ようにする必要があります。
 * -------------          ------------------
 * | domainId | 1 - n | domainName |
 * -------------          ------------------
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class CurrentDomain extends Domain {

    public function __construct(Service $service)
    {
        $service->setCurrentDomain($this);
        $this->service = $service;
    }

    public function getDomainId()
    {
        return $this->domainId;
    }

    public function getDomainName()
    {
        return $this->domainName;
    }

    public function setDomainId($domainId)
    {
        if (isset($this->domainId)) {
            throw new DomainException('domainId is immutable');
        }
        $this->domainId = $domainId;
    }

    public function setDomainName($domainName)
    {
        if (isset($this->domainName)) {
            throw new DomainException('domainName is immutable');
        }
        $this->domainName = (string) $domainName;
    }
}
