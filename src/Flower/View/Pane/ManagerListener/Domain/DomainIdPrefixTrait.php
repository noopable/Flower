<?php

/**
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\ManagerListener\Domain;

use Flower\View\Pane\Exception\RuntimeException;

/**
 * Description of DomainIdPrefixTrait
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
trait DomainIdPrefixTrait
{
    protected $domainPrefix = 'd_';

    public function getDefaultNamespace()
    {
        if (! isset($this->defaultNamespace)) {
            return 'flower_view_pane_domain_cache';
        }

        return $this->defaultNamespace;
    }

    public function getStorageOptions()
    {
        if (!$domainService = $this->getDomainService()) {
            throw new RuntimeException('This listener requires DomainService');
        }
        $domain = $domainService->getCurrentDomain();
        $domainId = $domain->getDomainId();

        if (!isset($domainId)) {
            throw new RuntimeException('This listener needs currentDomainId but not set');
        }

        $storageOptions = $this->storageOptions;
        //@see Zend\Cache\StorageFactory::factory
        if (!isset($storageOptions['adapter'])) {
            throw new RuntimeException('Missing "adapter"');
        }

        $adapterOptions = array();

        if (isset($storageOptions['adapter']['options'])) {
            $adapterOptions = $storageOptions['adapter']['options'];
        }

        if (isset($storageOptions['options'])) {
            $adapterOptions = array_merge($adapterOptions, $storageOptions['options']);
            unset($storageOptions['options']);
        }

        if (!isset($adapterOptions['namespace'])) {
            $adapterOptions['namespace'] = $this->getDefaultNamespace();
        }

        $adapterOptions['namespace'] = $this->domainPrefix . (string) $domainId . $adapterOptions['namespace'];

        $storageOptions['adapter']['options'] = $adapterOptions;

        return $storageOptions;
    }
}
