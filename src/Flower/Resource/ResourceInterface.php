<?php
namespace Flower\Resource;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Zend\Permissions\Acl;

/**
 * リソース管理の旧版
 * ※ ただし、pageモジュールで使用しているので、Pageモジュールのリファクタリングを優先してください。
 */
interface ResourceInterface extends Acl\Resource\ResourceInterface
{
    public function getResourceClass();
    public function getResourceId();
    
}