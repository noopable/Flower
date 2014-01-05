<?php
namespace Flower\File\Spec\Resolver;
/*
 * 
 * 
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
use Flower\File\FileInfo;
use Flower\File\Resolver\ResolverInterface;
/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ResolveSpecInterface
{

    public function configure();
    
    /**
     * 
     * @param \Flower\File\Resolver\ResolverInterface $resolver
     */
    public function setResolver(ResolverInterface $resolver);
    /**
     * 
     * @return \Flower\File\Resolver\ResolverInterface;
     */
    public function getResolver();
    
    public function isValid(FileInfo $fileInfo);
    
    public function getExtensions();
    
    public function getPathStack();
    
    public function getMap();
    
}
