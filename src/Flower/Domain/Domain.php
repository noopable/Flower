<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

/**
 * Description of Domain
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Domain implements DomainInterface {
    
    protected $domainId;
    
    protected $domainName;
    
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
        $this->domainId = (int) $domainId;
    }

    public function setDomainName($domainName)
    {
        $this->domainName = (string) $domainName;
    }
}
