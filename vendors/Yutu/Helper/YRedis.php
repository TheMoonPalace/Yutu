<?php
/**
 * Redis.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/6/5 0005
 * Time: 15:23
 */

namespace Yutu\Helper;


use Yutu\Env;

class YRedis
{
    /**
     * @var \Redis
     */
    private $phpRedis;

    /**
     * @var \Swoole\Redis
     */
    private $swoRedis;

    /**
     * @var YRedis
     */
    private static $instance;

    /**
     * Redis constructor.
     */
    private function __construct()
    {

    }

    /**
     * @return YRedis
     */
    public static function I()
    {
        if (empty(self::$instance))
        {
            $dbId = Env::Config("redis-db", 0);
            $port = Env::Config("redis-port", 6379);
            $host = Env::Config("redis-host", "127.0.0.1");

            self::$instance = new self();

            // TODO 同步redis
            self::$instance->phpRedis = new \Redis();

            if (!self::$instance->phpRedis->connect($host, $port)) {
                Logger::ExtremelySerious();
            }

            self::$instance->phpRedis->select($dbId);

            // TODO 异步redis
//            self::$instance->swoRedis = new \Swoole\Redis();
        }

        return self::$instance;
    }

    /**
     * @return bool
     */
    public static function T()
    {
        $port = Env::Config("redis-port", 6379);
        $host = Env::Config("redis-host", "127.0.0.1");

        $redis = new \Redis();

        if (!$redis->connect($host, $port)) {
            return false;
        }

        $redis->close();
        return true;
    }

    /**
     * @param $key
     * @return string
     */
    public function Pop($key)
    {
        return $this->phpRedis->lPop($key);
    }

    /**
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function Push($key, $value)
    {
        return $this->phpRedis->lPush($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function Set($key, $value)
    {
        return $this->phpRedis->set($key, $value);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function Get($key)
    {
        return $this->phpRedis->get($key);
    }
}