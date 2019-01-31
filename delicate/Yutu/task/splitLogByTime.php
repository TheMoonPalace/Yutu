<?php
/**
 * splitLogByTime.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/21 0021
 * Time: 15:05
 */

namespace Yutu\task;


use Yutu\moon\Env;
use Yutu\interfaces\ITask;

/**
 * 凌晨执行当天日志归档
 * Class splitLogByTime
 * @package Yutu\task
 */
class splitLogByTime implements ITask
{
    /**
     * @return int|mixed
     */
    public function Type()
    {
        return ITask::TimeTask;
    }

    /**
     * @return mixed|string
     */
    public function Time()
    {
        return "00:00";
    }

    /**
     * @return mixed|void
     */
    public function Executor()
    {
        clearstatcache();

        $file = PATH_LOGS . "/" . Env::YUTU_LOG_FILE;

        // 服务器日志
        if (!is_file($file)) {
            return;
        }

        $path = PATH_BACKUP . "/" . date("Ymd") . "/";
        !is_dir($path) && mkdir($path);

        $newFile = str_replace(".log", "-" . time() . ".log", Env::YUTU_LOG_FILE);
        $newDestination = $path . "/" . $newFile;
        rename($file, $newDestination);

        // 通知swoole重新加载日志文件
        $serverPid = Env::ServerPid();
        !empty($serverPid) && exec("kill -34 {$serverPid}");
    }
}