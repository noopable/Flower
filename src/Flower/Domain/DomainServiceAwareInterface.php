<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface DomainServiceAwareInterface
{

    public function setDomainService(Service $service);

    public function getDomainService();
}
