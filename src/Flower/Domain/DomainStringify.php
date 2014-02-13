<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

/**
 * Description of DomainStringify
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class DomainStringify
{

    protected $domain;

    protected $type = 'domainName';

    public function __construct(DomainInterface $domain, $type = null)
    {
        $this->domain = $domain;
        if (isset($type)) {
            $this->type = $type;
        }
    }

    public function __toString()
    {
        $method = 'get' . ucfirst($this->type);
        if (method_exists($this->domain, $method)) {
            return (string) $this->domain->$method();
        }
        return '';
    }
}
