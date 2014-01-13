<?php
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Resource;

use Flower\Model\AbstractEntity;

/**
 * 
 * ドキュメントで利用可能な配列等のリソースを提供できるエンティティ
 * 注）エンティティ等ドメインオブジェクトに過剰な責務を持たせない方がよいこともある。
 * 既存のEntityがあるなら単にResourceProviderInterfaceを実装するだけでよい。
 * また、リソースクラスに合ったリソースマッパーを使えばエンティティをクリーンに保てる。
 * 
 */
abstract class AbstractResourceProvider extends AbstractEntity implements ResourceProviderInterface
{
    abstract public function getResource();

}