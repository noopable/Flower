<?php
namespace Flower\File\Resolver;
/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

/**
 *
 * 主に、AggregateResolver内で、
 * 先行一致した場合に追加の解決を行うかどうかを設定可能であることを表明する。
 * 
 * 
 * 
 * 
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface ResolveTerminatorInterface {
    
    /**
     * 
     * parameter details
     * boolean = set mayTerminate option
     * null    = get mayTerminate option
     * object  = delegate mayTerminate option
     * 
     * @param null|boolean|ResolveTerminatorInterface $mayTerminate
     * @return boolean
     */
    public function mayTerminate($mayTerminate = null);
            
}

