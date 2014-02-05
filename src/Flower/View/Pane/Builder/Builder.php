<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane\Builder;

use Zend\Stdlib\ArrayUtils;
use Flower\Exception\IllegalClassException;
use Flower\View\Pane\Exception\PaneClassNotFoundException;
use Flower\View\Pane\Exception\RuntimeException;
use Flower\View\Pane\PaneClass\PaneInterface;
use Flower\View\Pane\PaneEvent;

/**
 * Paneツリーの定義配列からPaneオブジェクトツリーをビルドします。
 * もし、対象とするPaneが多様化し、Factory過程も分岐するなら、
 * AbstractPluginFactoryを含む形の別のBuilderを作成・使用
 * することを検討してください。
 * 利用使途と実装のバランスで現在はこのBuilderを使います。
 *
 * @author tomoaki
 */
class Builder
{

    protected $paneClass = 'Flower\View\Pane\PaneClass\Pane';

    protected $sizeToClassFunction;

    protected $escaper;

    protected $defaultPaneFactory = 'Flower\View\Pane\PaneFactory';

    public function __construct($options = array())
    {
        if (isset($options['pane_class'])) {
            $this->setPaneClass($options['pane_class']);
        }

        if (isset($options['size_to_class_function']) && is_callable($options['size_to_class_function'])) {
            $this->sizeToClassFunction = $options['size_to_class_function'];
        }
    }

    /**
     * 親PaneはgetTargetで与えられる。
     * もし文字列ならPaneIdである。
     * @param \Flower\View\Pane\PaneEvent $e
     * @return type
     */
    public function onBuild(PaneEvent $e)
    {
        $target = $e->getTarget();

        if ($target instanceof PaneInterface) {
            $pane = $this->build($e->getParams(), $target);
        } else {
            $pane = $this->build($e->getParams());
        }

        $e->setTarget($pane);

        return $pane;
    }

    /**
     * これから作成しようとするPaneの設定を$configに配列で渡す。
     * HashArrayは現在Paneの設定情報
     * Hashのうちinnerで渡される値については
     *  リストならPane構築情報のリスト
     *  ハッシュなら単一Paneの構成情報
     *
     * よって、buildでは、設定情報からinnerを分離し、現在PaneをFactory
     * 次にinnerをFactoryして現在Paneにinsertする
     *
     * 出来上がったPaneを返します。
     * buildが返すのは単一のPaneである。
     * 最初にコールされるときは、通常ではinner要素が必須となる。
     * 再帰的にコールされるときは親Paneにinsertした上で、単一のPaneを返す。
     * 初期configにListを指定されてもbuildは複数のpaneを返すことはない。
     *
     * @param array $config pane structure configuration
     * @param PaneInterface|null $parent
     * @return PaneInterface
     */
    public function build(array $config, PaneInterface $parent = null)
    {
        if (isset($config['pages'])) {
            $innerConfig = $config['pages'];
            unset($config['pages']);
        }

        if (isset($config['inner'])) {
            if (isset($innerConfig)) {
                if (ArrayUtils::isHashTable($innerConfig)) {
                    $innerConfig = array($innerConfig);
                }
                if (ArrayUtils::isHashTable($config['inner'])) {
                    $innerConfig[] = $config['inner'];
                } else {
                    $innerConfig = array_merge($innerConfig, $config['inner']);
                }
            } else {
                $innerConfig = $config['inner'];
            }
            unset($config['inner']);
        }

        $current = $this->createFromConfig($config);

        if (!empty($innerConfig)) {
            if (ArrayUtils::isList($innerConfig)) {
                foreach ($innerConfig as $c) {
                    $child = $this->build($c, $current);
                }
            }
            elseif(is_array($innerConfig)) {
                $child = $this->build($innerConfig, $current);
            }
        }

         if (isset($parent)) {
             $parent->insert($current, $current->getOrder());
         }

         return $current;
    }

    public function createFromConfig(array $config)
    {
        if (isset($config['factory_class'])) {
            $factoryClass = $config['factory_class'];
        } elseif (isset($config['pane_class'])) {
            if (! class_exists($config['pane_class'])) {
                throw new PaneClassNotFoundException('class not exists ' . $config['pane_class']);
            }
            if (! is_a($config['pane_class'], 'Flower\View\Pane\PaneClass\PaneInterface', true)) {
                throw new RuntimeException($config['pane_class'] . ' is not instance of PaneInterface');
            }
            $factoryClass = call_user_func($config['pane_class'] . '::getFactoryClass');
        } else {
            $factoryClass = $this->getDefaultPaneFactory();
        }

        if (isset($this->sizeToClassFunction) && !isset($config['size_to_class_function'])) {
            $config['size_to_class_function'] = $this->sizeToClassFunction;
        }

        return call_user_func($factoryClass . '::factory', $config);
    }

    public function setDefaultPaneFactory($paneFactory)
    {
        if (!is_subclass_of($paneFactory, 'Flower\View\Pane\PaneFactoryInterface', true)) {
            throw new RuntimeException('defaultPaneFactory should implement PaneFactoryInterface :' . $paneFactory);
        }
        if ($paneFactory instanceof PaneFactoryInterface) {
            $paneFactory = get_class($paneFactory);
        }
        $this->defaultPaneFactory = $paneFactory;
    }

    public function getDefaultPaneFactory()
    {
        return $this->defaultPaneFactory;
    }

    public function setPaneClass($pane)
    {
        if ($pane instanceof PaneInterface) {
            $this->paneClass = get_class($pane);
        } elseif (is_string($pane)) {
            if (!is_subclass_of($pane, 'Flower\View\Pane\PaneClass\PaneInterface', true)) {
                throw new IllegalClassException('Specified class name is ' .  $pane . ' but not implements PaneInterface');
            }
            $this->paneClass = $pane;
        }

        $this->setDefaultPaneFactory(call_user_func($this->paneClass . '::getFactoryClass'));

        return $this;
    }

    public function getSizeToClassFunction()
    {
        return $this->sizeToClassFunction;
    }
}