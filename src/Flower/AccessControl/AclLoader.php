<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2015 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\AccessControl;

use Zend\Permissions\Acl\Acl as ZendAcl;

/**
 *
 *
 * @author tomoaki
 */
class AclLoader {
    protected $__acl;

    protected $__aclScript;
    protected $__isLoaded;

    protected $__vars;

    public function __construct($aclScript, array $vars = array())
    {
        $this->setAclScript($aclScript);
        $this->setVars($vars);
    }

    public function setAclScript($aclScript)
    {
        $this->__isLoaded = false;
        $this->__aclScript = $aclScript;
    }

    public function setVar($key, $value)
    {
        $this->__isLoaded = false;
        $this->__vars[$key] = $value;
    }

    public function setVars(array $vars)
    {
        $this->__isLoaded = false;
        $this->__vars = $vars;
    }

    public function load()
    {
        if (!isset($this->__acl)) {
            $this->resetAcl();
        }
        $acl = $this->__acl;
        if (! $this->__isLoaded) {
            $__vars = $this->__vars;
            foreach (array_keys($this->__vars) as $k) {
                if (is_int($k)) {
                    unset($__vars[$k]);
                }
            }
            unset($k);

            if (array_key_exists('this', $__vars)) {
                unset($__vars['this']);
            }
            if (array_key_exists('acl', $__vars)) {
                unset($__vars['acl']);
            }
            extract($__vars);
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
