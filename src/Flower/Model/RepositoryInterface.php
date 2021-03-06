<?php

/*
 *
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */
namespace Flower\Model;

/**
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
interface RepositoryInterface {
    /**
     *
     * @return void
     */
    public function initialize();

    /**
     * this repository is initialized or not.
     *
     * @return bool
     */
    public function isInitialized();
}

