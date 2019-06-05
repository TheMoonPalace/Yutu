<?php
/**
 * Server.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/12 0012
 * Time: 10:38
 */

namespace Yutu\Net;


use Yutu\Env;

class HttpServer
{
    /**
     * @var \Swoole\Server
     */
    public $http;

    /**
     * @var HttpServer
     */
    private static $instance;

    /**
     * Server constructor.
     */
    private function __construct()
    {

    }

    /**
     * @return HttpServer
     */
    public static function I()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // 创建HTTP服务器
    public function Create()
    {
        // 端口从配置获取，默认8080
        $this->http = new \Swoole\http\Server("0.0.0.0", Env::Config("port", 8080));

        // 设置swoole的相关配置
        $this->http->set([
            'pid_file'  => PATH_CACHE . '/' . Env::YUTU_PID_FILE,
            'log_file'  => PATH_LOGS . '/' . Env::YUTU_LOG_FILE,
            'log_level' => SWOOLE_LOG_INFO, // swoole 日志等级 https://wiki.swoole.com/wiki/page/538.html
            'daemonize'  => Env::Config("daemonize", false),
            'worker_num' => Env::Config("worker-num", 4),
            'task_worker_num' => 1,
        ]);
    }

    // 注册Swoole Server事件
    public function Register()
    {
        $this->http->on("ManagerStart", "Yutu\Event::ManagerStart");
        $this->http->on("ManagerStop", "Yutu\Event::ManagerStop");
        $this->http->on("WorkerStart", "Yutu\Event::WorkerStart");
        $this->http->on("WorkerError", "Yutu\Event::WorkerError");

        $this->http->on("Task", "Yutu\Event::Task");
        $this->http->on("Start", "Yutu\Event::Start");
        $this->http->on("Finish", "Yutu\Event::Finish");
        $this->http->on("Request", "Yutu\Event::NewRequest");
    }
}