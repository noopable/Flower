<?php
namespace Flower\File\Spec;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\File\Gateway\GatewayInterface;
use Flower\File\Adapter\AdapterInterface;
use Flower\File\Spec\Cache\CacheSpecInterface;
use Flower\File\Spec\Resolver\ResolveSpecInterface;
use Flower\File\Spec\Merge\MergeSpecInterface;
/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface SpecInterface {
    
    public function __construct($spec = null);
    
    public function configure();
    
    public function setGateway(GatewayInterface $gateway);
    
    public function getGateway();
    
    public function setCacheSpec(CacheSpecInterface $cacheSpec);
    
    public function getCacheSpec();
    
    public function setResolveSpec(ResolveSpecInterface $resolveSpec);
    
    public function getResolveSpec();
    
    public function setMergeSpec(MergeSpecInterface $mergeSpec);
    
    public function getMergeSpec();
            
    public function setFileAdapter(AdapterInterface $fileAdapter);
    
    public function getFileAdapter();
}

