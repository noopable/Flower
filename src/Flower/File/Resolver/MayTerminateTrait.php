<?php
namespace Flower\File\Resolver;
/*
 *
 *
 * @copyright Copyright (c) 2013-2013 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
/**
 * Description of MayTerminateTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait MayTerminateTrait {

    /**
     * 標準のAggregateResolverでは、先行一致で終了する。
     * そのため、デフォルト値はtrue
     * 
     * @var boolean 
     */
    protected $mayTerminate = true;
    
    public function mayTerminate($mayTerminate = null)
    {
        //set option
        if (is_bool($mayTerminate)) {
            $this->mayTerminate = $mayTerminate;
        }
        
        //delegate option
        if (is_object($mayTerminate) && $mayTerminate instanceof ResolveTerminatorInterface) {
            $this->mayTerminate = $mayTerminate->mayTerminate();
        }
        
        // at last return option
        return $this->mayTerminate; 
    }
}
