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

    /**
     * @param int $length
     */
    public static function createNewPassword($length = 10, $charList = null)
    {
        if (null === $charList) {
            $charArray = array(
                'abcdefghijkmnopqrtuwxy',
                'ABCDEFGHJKLMNPQRSTUVWXYZ',
                '123456789',
                '#$-=?@[]_',
            );
            $charList = implode('', $charArray);
        } elseif (is_string($charList)) {
            $charArray = array($charList);
        }

        $result = array();
        $i = 1;
        $indexes = range(1, $length);
        $iterator = new \ArrayIterator($charArray);

        while (count($indexes)) {
            $list = $iterator->current();
            $i = array_shift($indexes);
            $result[$i] = substr($list, rand(0, strlen($list) -1), 1);
            shuffle($indexes);
            $iterator->next();
            if (! $iterator->valid()) {
                shuffle($charArray);
                $iterator = new \ArrayIterator($charArray);
                $iterator->rewind();
            }
        }
        ksort($result);
        return implode('', $result);
    }

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
            $str = hash('sha256', sha1($prefix . $str . $salt) . $salt);
        } while (--$counter > 0);

        return $str;
    }
}
