<?php
/**
 * Event.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 11:13
 */

namespace Yutu\moon;


use Yutu\helper\Logger;
use Yutu\helper\TaskForce;
use Yutu\interfaces\IServerEvent;
use Yutu\net\http\Controller;

class Event implements IServerEvent
{
    /**
     * @param \Swoole\Server $server
     * @return mixed|void
     */
    public static function ManagerStart(\Swoole\Server $server)
    {
        // 修改管理进程名
        swoole_set_process_name("YT-Manager");
    }

    // stop
    public static function ManagerStop()
    {

    }

    /**
     * @param \Swoole\Server $server
     * @param $workerId
     * @return mixed|void
     */
    public static function WorkerStart(\Swoole\Server $server, $workerId)
    {
        Logger::$processId = $server->worker_pid;

        if ($workerId >= $server->setting['worker_num']) {
            swoole_set_process_name("YT-TaskDB"); // task进程
        } else {
            swoole_set_process_name("YT-Worker"); // worker进程
        }
    }

    /**
     * @param \Swoole\Server $server
     * @return mixed|void
     */
    public static function WorkerError(\Swoole\Server $server)
    {
        Logger::Exception(error_get_last());
    }

    /**
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     * @return mixed|void
     */
    public static function NewRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $uri = $request->server['request_uri'];
        $ctr = new Controller($request, $response);
        $handles = explode("/", $uri);

        if (empty($handles[1])) {
            $ctr->writeAll(null, "404 Not Found", 404); return ;
        }

        $handle = "app\\controller\\" . $handles[1];
        $handleFunc = isset($handles[2]) ? $handles[2] : "index";

        try {
            $hd = new $handle($request, $response);

            if (!method_exists($hd, $handleFunc)) {
                $ctr->writeAll(null, "Method Not Found: $handleFunc", 404); return ;
            }

            $res = $hd->{$handleFunc}();
            !method_exists($hd, "writeAll") ? $ctr->writeAll($res) : !$hd->isReturn && $hd->writeAll($res);
        } catch (\ParseError $e) {
            Logger::Exception($e);
            $ctr->writeAll(null, "Internal Server Error", 500);
        } catch (\Exception $e) {
            Logger::Exception($e);
            $ctr->writeAll(null, "Internal Server Error", 500);
        } catch (\Error $e) {
            Logger::Exception($e);
            $ctr->writeAll(null, "Internal Server Error", 500);
        }
    }

    /**
     * @param \Swoole\Server $server
     * @param \Swoole\Server\Task $task
     * @return mixed|void
     */
    public static function Task(\Swoole\Server $server, \Swoole\Server\Task $task)
    {
        // TODO
    }

    /**
     * @param \Swoole\Server $server
     * @param $task_id
     * @param $data
     * @return mixed|void
     */
    public static function Finish(\Swoole\Server $server, $task_id, $data)
    {
        // TODO
    }

    /**
     * @param \Swoole\Server $server
     * @return mixed|void
     */
    public static function Start(\Swoole\Server $server)
    {
        if (!Env::Config("daemonize"))
        {
            $v = Env::YUTU_VERSION;
            $port = Env::Config("port");
            $taskNumber = $server->setting['task_worker_num'];
            $workerNumber = $server->setting['worker_num'];

            echo <<<EOT
                                           
,--.   ,--. ,--. ,--. ,--------. ,--. ,--. 
 \  `.'  /  |  | |  | '--.  .--' |  | |  | 
  '.    /   |  | |  |    |  |    |  | |  | 
    |  |    '  '-'  '    |  |    '  '-'  ' 
    `--'     `-----'     `--'     `-----'  
                                                                                       
         <Yutu HTTP Server v{$v}>       
                                                         
 Port: {$port}    Work-Num: {$workerNumber}    Task-Num: {$taskNumber}

EOT;
            return ;
        }

        Logger::Record("Server Start");

        // $server->master_pid;
        // $server->manager_pid
    }
}