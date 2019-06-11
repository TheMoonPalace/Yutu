<?php
/**
 * Routes.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/6/11 0011
 * Time: 14:42
 */

namespace Yutu;


class Routes
{
    // 自定义路由映射
    private static $routes = [];
    // flag
    private static $isLoad = false;

    // 加载路由配置文件
    private static function load()
    {
        $file = PATH_APP . "/Route.php";

        if (file_exists($file)) {
            self::$routes = include_once $file;
            self::$isLoad = true;
        }
    }

    /**
     * 解析路由
     * @param $uri
     * @return array
     */
    public static function Resolver($uri)
    {
        if (!self::$isLoad) {
            self::load();
        }

        $theKey = "";
        $theClass = "";
        $theMethod = "";

        if (!isset(self::$routes[$uri]))
        {
            foreach (self::$routes as $key => $value)
            {
                $pat = explode("*", $key);

                if (count($pat) < 2 || empty($pat[0])) {
                    continue;
                }

                if (strrpos($uri, $pat[0]) === 0 && strlen($theKey) < strlen($key)) {
                    $theKey = $pat[0];
                }
            }
        } else {
            $theKey = $uri;
        }

        if (!empty($theKey) && isset(self::$routes[$theKey]))
        {
            $forward = explode("/", self::$routes[$theKey]);
            $theClass = $forward[0];
            $theMethod = $forward[1];
        }

        return ['class' => $theClass, 'method' => $theMethod];
    }
}