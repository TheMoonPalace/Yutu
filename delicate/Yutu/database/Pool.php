<?php
/**
 * Pool.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/2/13 0013
 * Time: 9:39
 */

namespace Yutu\database;


use Yutu\helper\Logger;
use Yutu\interfaces\IDatabase;
use Yutu\moon\Env;
use Yutu\types\YutuDBException;

/**
 * Class Pool
 * @package Yutu\database
 */
class Pool
{
    // 数据库类型
    private $type;
    // 端口
    private $port;
    // 地址
    private $host;
    // 数据库名称
    private $name;
    // 用户名
    private $user;
    // 用户密码
    private $pswd;

    /**
     * @var IDatabase
     */
    private $dao;

    /**
     * @var bool
     */
    private $worker = true;

    /**
     * @var \Swoole\Server
     */
    private $server = null;

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
     * @param null $server
     * @param bool $isWorker
     * @return Pool
     */
    public static function I($server = null, $isWorker = true)
    {
        if (empty(self::$instance))
        {
            self::$instance = new self();
            self::$instance->worker = $isWorker;
            self::$instance->server = $server;

            self::$instance->type = Env::Config("db-type", "mysql");
            self::$instance->port = Env::Config("db-port", "3306");

            self::$instance->host = Env::Config("db-host");
            self::$instance->name = Env::Config("db-name");
            self::$instance->user = Env::Config("db-user");
            self::$instance->pswd = Env::Config("db-pswd");
        }

        return self::$instance;
    }

    /**
     * @param $method
     * @param $sql
     * @return string
     */
    public function Call($method, $sql)
    {
        return $this->server->taskwait(["method" => $method, "data" => $sql], 30);
    }

    /**
     * @param $method
     * @param $sql
     */
    public function Work($method, $sql)
    {
        if ($this->worker) {
            return ;
        }

        $result = null;

        if (empty($this->dao)) {
            $this->connect();
        }

        try {
            $result = $this->dao->{$method}($sql);
        } catch (YutuDBException $e) {
            switch ($e->getCode())
            {
                // 关鸡重连
                case 2006:
                    $this->connect(); break;
                default:
                    echo "ERR CODE: " .  $e->getCode() . "\n";
                    Logger::Exception($e);
            }
        }

        $this->server->finish($result);
    }

    //  链接数据库
    private function connect()
    {
        try {
            switch (strtolower($this->type))
            {
                case "mysql":
                    $dsn = "mysql:dbname={$this->name};host={$this->host};port:{$this->port};";
                    $link = new \PDO($dsn, $this->user, $this->pswd, [\PDO::ATTR_PERSISTENT => true]);
                    $link->query("SET NAMES utf8");

                    $this->dao = new Mysql($link);
                    break;
                default:
            }
        } catch (\Exception $e) {
            Logger::Exception($e);
        }
    }
}