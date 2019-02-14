<?php
/**
 * Server.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 11:12
 */

namespace Yutu\moon;


use Yutu\database\Connection;
use Yutu\helper\DB;
use Yutu\helper\Logger;
use Yutu\helper\TaskForce;
use Yutu\net\http;

class YutuSw
{
    /**
     * @var YutuSw
     */
    private static $instance = null;

    /**
     * YutuSw constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return YutuSw
     */
    public static function I()
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    // 创建服务
    public function CreateHTTPServer()
    {
        http\Server::I()->Create();
        http\Server::I()->Register();

        // 环境检查
        $this->testRequirement();
        // master进程命名
        swoole_set_process_name("YT-Master");
        // 用于执行计划任务
        $taskProcess = TaskForce::I()->Init();

        http\Server::I()->http->addProcess($taskProcess);
        http\Server::I()->http->start();
    }

    // 重新加载当前app服务、重启所有worker进程
    public function ReloadHTTPServer()
    {
        $masterId = Env::ServerPid();

        if (empty($masterId)) {
            logger::ExtremelySerious("Reload: " . APP_NAME . " Not Exists");
        }

        exec("kill -" . SIGUSR1 . " {$masterId}"); return ;
    }

    // 重启
    public function RestartHTTPServer()
    {
        global $argv;
        $masterId = Env::ServerPid();

        if (empty($masterId)) {
            logger::ExtremelySerious("Restart: " . APP_NAME . " Not Exists");
        }

        exec("kill -" . SIGTERM . " " . $masterId);
        exec(str_replace(" ", "\ ", DI) . "/" . basename($argv[0]) . " ". Env::YUTU_SYS_START ." " . basename(APP_PATH));
    }

    // 停止当前app服务
    public function StopHTTPServer()
    {
        $masterId = Env::ServerPid();

        if (empty($masterId)) {
            Logger::ExtremelySerious("Stop: " . APP_NAME . " Not Exists");
        }

        exec("kill -" . SIGTERM . " {$masterId}");
    }

    // 检查服务必须的依赖项是否正常
    private function testRequirement()
    {

    }

}