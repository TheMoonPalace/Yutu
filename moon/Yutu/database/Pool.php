<?php
/**
 * Pool.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/14 0014
 * Time: 10:01
 */

namespace Yutu\database;


use Yutu\Env;
use Yutu\helper\Logger;

class Pool
{
    /**
     * @var \Swoole\Coroutine\Channel
     */
    private $queue = null;

    private $count = 0;

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
            self::$instance->queue = new \Swoole\Coroutine\Channel(10);
            self::$instance->type  = Env::Config("db-type", "mysql");

            for ($i = 0; $i < 10; $i++) {
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
        $dao = "Yutu\database\\" . ucfirst($this->type);
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

                $lin = $swm->connect([
                    'host' => '127.0.0.1',
                    'port' => 3306,
                    'user' => 'root',
                    'password' => 'root',
                    'database' => 'aowuka',
                ]);

                $this->queue->push($swm);

                break;
            default:
        }

    }

}