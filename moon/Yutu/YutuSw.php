<?php
/**
 * YutuSw.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/11 0011
 * Time: 10:56
 */

namespace Yutu;


use Yutu\net\HttpServer;
use Yutu\helper\Logger;

class YutuSw
{
    /**
     * @var YutuSw
     */
    private static $instance;

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
            self::$instance = new self();
        }

        return self::$instance;
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

    // 创建服务
    public function CreateHTTPServer()
    {
        HttpServer::I()->Create();
        HttpServer::I()->Register();

        // master进程命名
        swoole_set_process_name("YT-Master");

        HttpServer::I()->http->start();
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

    // 重启服务
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

}