<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl;

use Zend\Permissions\Acl\Acl as ZendAcl;
/**
 * @todo シリアライズしてキャッシュする？
 *
 * @author tomoaki
 */
class AclLoader {
    protected $__acl;
    
    protected $__aclScript;
    protected $__isLoaded;
    
    public function __construct($aclScript)
    {
        $this->__aclScript = $aclScript;
    }
    
    public function load()
    {
        if (!isset($this->__acl)) {
            $this->resetAcl();
        }
        $acl = $this->__acl;
        if (! $this->__isLoaded) {
            try {
                ob_start();
                include $this->__aclScript;
                ob_end_clean();
            } catch (\Exception $ex) {
                ob_end_clean();
                throw new Exception\RuntimeException('can\'t load acl from ' . realpath($this->__aclScript), 0,  $ex);
            }
        }
        $this->__isLoaded = true;
        return $acl;
    }
    
    public function setAcl(ZendAcl $acl)
    {
        $this->__acl = $acl;
    }
    
    public function resetAcl()
    {
        $this->__acl = new ZendAcl;
    }
    
    public function getAcl()
    {
        if (!isset($this->__acl)) {
            $this->resetAcl();
        }
        return $this->load();
    }
    
    public function __invoke()
    {
        return $this->load();
    }
}
