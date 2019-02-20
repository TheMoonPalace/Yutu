<?php
/**
 * Server.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 11:29
 */

namespace Yutu\net\http;


use Yutu\moon\Env;

class Server
{
    /**
     * @var \Swoole\Http\Server
     */
    public $http;

    /**
     * @var bool
     */
    private $flag = false;

    /**
     * @var Server
     */
    private static $server;

    /**
     * Server constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return Server
     */
    public static function I()
    {
        if (empty(self::$server)) {
            self::$server = new static();
        }

        return self::$server;
    }

    // create http server
    public function Create()
    {
        // 端口从配置获取，默认8080
        // 当前允许启动多个相同服务，框架层面不限制
        $this->http = new \Swoole\http\Server("0.0.0.0", Env::Config("port", 8080));

        // 设置swoole的相关配置
        // task进程用于数据库操作...
        $this->http->set([
            'pid_file'  => PATH_CACHE . '/' . Env::YUTU_PID_FILE,
            'log_file'  => PATH_LOGS . '/' . Env::YUTU_LOG_FILE,
            'log_level' => SWOOLE_LOG_INFO, // swoole 日志等级 https://wiki.swoole.com/wiki/page/538.html
            'daemonize'  => Env::Config("daemonize", false),
            'worker_num' => Env::Config("worker-num", 4),
            'task_worker_num' => Env::Config("db-pool", 10),
        ]);
    }

    // register * event
    public function Register()
    {
        $this->http->on("ManagerStart", "Yutu\moon\Event::ManagerStart");
        $this->http->on("ManagerStop", "Yutu\moon\Event::ManagerStop");
        $this->http->on("WorkerStart", "Yutu\moon\Event::WorkerStart");
        $this->http->on("WorkerError", "Yutu\moon\Event::WorkerError");

        $this->http->on("Task", "Yutu\moon\Event::Task");
        $this->http->on("Finish", "Yutu\moon\Event::Finish");
        $this->http->on("Start", "Yutu\moon\Event::Start");
        $this->http->on("Request", "Yutu\moon\Event::NewRequest");
    }

}