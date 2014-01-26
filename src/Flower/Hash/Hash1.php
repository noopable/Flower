<?php

/*
 *
 * @copyright Copyright (c) 2013-2014 KipsProduction (http://www.kips.gr.jp)
 * @license   http://www.kips.gr.jp/newbsd/LICENSE.txt New BSD License
 */

namespace Flower\Hash;

use Flower\Exception\RuntimeException;
use Flower\Module;

/**
 * ハッシュメソッドの１
 * 変更せずに仕様変更したいときはクラスを追加してください。
 *
 * @author Tomoaki Kosugi <kosugi at kips.gr.jp>
 */
class Hash1 {

    protected static $salt;

    public static function getSalt()
    {
        if (isset(self::$salt)) {
            return self::$salt;
        }
        self::$salt = Module::getSalt();

        return self::$salt;
    }

    public static function hash($credential, $prefix = '')
    {
        if (!is_string($credential)) {
            throw new RuntimeException('credential is not string');
        }
        if (!is_string($prefix)) {
            throw new RuntimeException('prefix is not string');
        }
        //I'm sorry for hard code hash please give me better code.
        $salt = self::getSalt();
        $max = 20;
        $str = $credential;
        $len = strlen($str);
        $counter = ($len > 16) ? 5 : $max - $len;
        do {
            $str = sha1($prefix . $str . $salt);
        } while (--$counter > 0);

        return $str;
    }
}
