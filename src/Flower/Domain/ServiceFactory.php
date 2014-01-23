<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
/**
 * 特にFactoryで仕事をしない場合、Invokablesに登録するだけでも問題ない。
 * ServiceLocatorスコープ単位でCurrentDomainをシングルトン化するため
 * サービスロケータに登録する
 *
 * ServiceをServiceLocatorから取得し、EventManagerへ登録するように
 * Module.php等で設定することで動作します。
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class ServiceFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $service = new Service;
        return $service;
    }
}
