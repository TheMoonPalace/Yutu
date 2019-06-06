<?php
/**
 * Pool.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/14 0014
 * Time: 10:01
 */

namespace Yutu\Database;


use Yutu\Env;
use Yutu\Helper\Logger;

class Pool
{
    /**
     * @var \Swoole\Coroutine\Channel
     */
    private $queue = null;

    /**
     * 最大的连接数
     * @var int
     */
    private $max = 0;

    /**
     * 最小连接数
     * @var int
     */
    private $min = 1;

    /**
     * 数据库类型
     * @var string
     */
    private $type = '';

    /**
     * @var Pool
     */
    private static $instance;

    /**
     * Pool constructor.
     */
    private function __construct()
    {

    }

    /**
     * @return Pool
     */
    public static function I()
    {
        if (empty(self::$instance))
        {
            self::$instance = new self();
            self::$instance->max = Env::Config('db-pool', 4);
            self::$instance->type = Env::Config("db-type", "mysql");
            self::$instance->queue = new \Swoole\Coroutine\Channel( self::$instance->max);

            for ($i = 0; $i < self::$instance->max; $i++) {
                self::$instance->connect();
            }
        }

        return self::$instance;
    }

    /**
     * @param $func
     * @param $params
     * @return null
     */
    public function Call($func, ...$params)
    {
        $dao = "Yutu\Database\\" . ucfirst($this->type);
        $lin = $this->queue->pop();
        $res = null;

        try {
            $res = (new $dao($lin))->{$func}(...$params);
        } catch (\Exception $e) {
            Logger::Exception($e);
        } catch (\Error $e) {
            Logger::Exception($e);
        }

        $this->queue->push($lin);
        return $res;
    }

    // 链接数据库
    private function connect()
    {
        switch ($this->type)
        {
            case "mysql":
                $swm = new \Swoole\Coroutine\MySQL();

                $swm->connect([
                    'host' => Env::Config("db-host", "127.0.0.1"),
                    'port' => Env::Config("db-port", 3306),
                    'user' => Env::Config("db-user", ""),
                    'database' => Env::Config("db-name", ""),
                    'password' => Env::Config("db-password", ""),
                ]);

                $this->queue->push($swm);

                break;
            default:
        }

    }

}