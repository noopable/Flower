<?php
namespace Flower\File\Spec;
/* 
 * 
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
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
Trait SpecSetterGetterTrait 
{
    protected $gateway;
    
    protected $resolveSpec;
    
    protected $cacheSpec;
    
    protected $mergeSpec;
    
    protected $fileAdapter;
    
    public function setGateway(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }
    
    public function getGateway()
    {
        return $this->gateway;
    }
    
    public function setCacheSpec(CacheSpecInterface $cacheSpec)
    {
        $this->cacheSpec = $cacheSpec;
    }
    
    public function getCacheSpec()
    {
        return $this->cacheSpec;
    }
    
    public function setResolveSpec(ResolveSpecInterface $resolveSpec)
    {
        $this->resolveSpec = $resolveSpec;
    }
    
    public function getResolveSpec()
    {
        return $this->resolveSpec;
    }
    
    public function setMergeSpec(MergeSpecInterface $mergeSpec)
    {
        $this->mergeSpec = $mergeSpec;
    }
            
    public function getMergeSpec()
    {
        return $this->mergeSpec;
    }
            
    public function setFileAdapter(AdapterInterface $fileAdapter)
    {
        $this->fileAdapter = $fileAdapter;
    }
    
    public function getFileAdapter()
    {
        return $this->fileAdapter;
    }
      
}
