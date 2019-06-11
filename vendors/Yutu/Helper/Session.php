<?php
/**
 * Session.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/6/6 0006
 * Time: 11:16
 */

namespace Yutu\Helper;


class Session
{
    /**
     * @var string
     */
    private static $tag = "yutusession";

    /**
     * 获取session， 没有则设置
     * @param string $key
     * @param string $value
     * @return string|null
     */
    public static function Key($key = "", $value = "")
    {
        if (!empty($key) && ($value !== '' && $value !== false)) {
            self::Set($key, $value); return $value;
        }

        return isset($_SESSION[self::$tag][$key]) ? $_SESSION[self::$tag][$key] : null;
    }

    /**
     * @param $key
     * @param $value
     */
    public static function Set($key, $value)
    {
        if (is_null($key)) {
            $_SESSION[self::$tag] = null; return ;
        }

        $_SESSION[self::$tag][$key] = $value;
    }
}