<?php

/**
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\View\Pane;

use Flower\View\Pane\PaneClass\PaneInterface;

/**
 *
 * @author tomoaki
 */
interface BuilderInterface
{

    /**
     * これから作成しようとするPaneの設定を$configに配列で渡す。
     * 設定から直接Paneをビルドするときは、$current = nullとして渡す。
     * 既存のPaneがあり、子を追加したいときには、$current = $parentPaneとして渡す
     *
     * 出来上がったPane　$currentを返します。
     *
     * @param array $config pane structure configuration
     * @param PaneInterface|null $current
     * @return PaneInterface
     */
    public function build(array $config, PaneInterface $current = null);

    public function onBuild(PaneEvent $e);
}
