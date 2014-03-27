<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Domain;

use Flower\Exception\DomainException;
use Zend\Http\Request;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
/**
 * Description of Service
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Service implements ListenerAggregateInterface {

    protected $currentDomain;

    protected $currentDomainName;

    protected $currentDomainId;

    protected $hostMap = array();

    protected $listeners = array();

    protected $domainRoutes;

    public function setCurrentDomain(CurrentDomain $currentDomain)
    {
        if (isset($this->currentDomain) && ($this->currentDomain !== $currentDomain)) {
            throw new DomainException('currentDomain is immutable');
        }
        $this->currentDomain = $currentDomain;
    }

    public function createCurrentDomain()
    {
        if (isset($this->currentDomain)) {
            $currentDomain = $this->currentDomain;
        } else {
            /**
             * Double dispatch process
             * CurrentDomain::__construct invoke $service->setCurrentDomain(self)
             */
            $currentDomain = new CurrentDomain($this);
        }

        if (isset($this->currentDomainId)
                && null === $currentDomain->getDomainId()) {
            $currentDomain->setDomainId($this->currentDomainId);
        }

        if (isset($this->currentDomainName)
                && null === $currentDomain->getDomainName()) {
            $currentDomain->setDomainName($this->currentDomainName);
        }
        return $currentDomain;
    }

    public function createDomain($domainId, $domainName)
    {
        $domain = new Domain;
        $domain->setDomainId($domainId);
        $domain->setDomainName($domainName);
        return $domain;
    }

    /**
     * singleton in scope
     *
     * @return type
     */
    public function getCurrentDomain()
    {
        if (!isset($this->currentDomain)) {
            $this->createCurrentDomain();
        }
        return $this->currentDomain;
    }

    public function setCurrentDomainName($domainName)
    {
        if (isset($this->currentDomainName)) {
            throw new DomainException('domain name is immutable');
        }
        $this->currentDomainName = $domainName;
        $this->createCurrentDomain();
    }

    public function setCurrentDomainId($domainId)
    {
        if (isset($this->currentDomainId)) {
            throw new DomainException('domain id is immutable');
        }
        $this->currentDomainId = $domainId;
        $this->createCurrentDomain();
    }

    public function onBootstrap(MvcEvent $e)
    {
        $application = $e->getApplication();
        if ($application instanceof Application) {
            $serviceLocator = $application->getServiceManager();
            if ($serviceLocator->has('Di')) {
                $di = $serviceLocator->get('Di');
                $im = $di->instanceManager();
                $currentDomain = $this->getCurrentDomain();
                $im->addSharedInstance($currentDomain, 'Flower\Domain\CurrentDomain');
                $im->addTypePreference('Flower\Domain\DomainInterface', $currentDomain);
            }
        }
    }

    public function onRoute(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $domainName = $routeMatch->getParam('domain_name', null);
        $domainId = $routeMatch->getParam('domain_id', null);

        if (!isset($domainName)) {
            $request = $e->getRequest();
            if ($request instanceof Request) {
                $domainName = $request->getUri()->getHost();
            }
        }

        if (!isset($domainId)) {
            $hostMap = $this->getHostMap();

            if (!$hostMap) {
                $application = $e->getApplication();
                if ($application instanceof Application) {
                    $serviceLocator = $application->getServiceManager();
                    if ($serviceLocator->has('Config')) {
                        $config = $serviceLocator->get('Config');
                        if (isset($config['host_map'])) {
                            $hostMap = $config['host_map'];
                        }
                    }
                }
            }
            
            $nDomainName = strtolower($domainName);
            if (is_array($hostMap) && isset($hostMap[$nDomainName])) {
                $domainId = $hostMap[$nDomainName];
            } else {
                $domainId = -1;
            }
        }

        $this->setCurrentDomainName($domainName);
        $this->setCurrentDomainId($domainId);
    }

    public function attach(EventManagerInterface $events)
    {
        if (! isset($this->listeners['onBootstrap'])) {
            $this->listeners['onBootstrap'] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, [$this, 'onBootstrap']);
        }
        if (!isset($this->listeners['onRoute'])) {
            $this->listeners['onRoute'] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute']);
        }
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function setHostMap(array $hostMap)
    {
        $this->hostMap = $hostMap;
    }

    public function getHostMap()
    {
        return $this->hostMap;
    }
}
