<?php
/*
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

/**
 * ドキュメント空間での管理境界領域を識別する
 * 
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface DomainInterface {
    public function setDomainName($domainName);
    public function getDomainName();
    
    public function setDomainId($domainId);
    public function getDomainId();
}
