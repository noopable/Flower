<?php

/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

/**
 * Description of DomainAwareTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait DomainAwareTrait {
    protected $domain;
    
    /**
     * 
     * @param DomainInterface $domain
     */
    public function setDomain(DomainInterface $domain)
    {
        $this->domain = $domain;
    }
    
    /**
     * 
     * @return DomainInterface
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
