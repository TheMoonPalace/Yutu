<?php
/**
 * Event.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/11 0011
 * Time: 10:56
 */

namespace Yutu;


use Yutu\Database\Pool;
use Yutu\Helper\Logger;
use Yutu\Helper\YRedis;
use Yutu\Interfaces\IServerEvent;
use Yutu\Net\Controller;
use Yutu\Type\CoroutineExitException;

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

    /**
     * stop
     * @return mixed|void
     */
    public static function ManagerStop()
    {
        // TODO: Implement ManagerStop() method.
    }

    /**
     * @param \Swoole\Server $server
     * @param $workerId
     * @return mixed|void
     */
    public static function WorkerStart(\Swoole\Server $server, $workerId)
    {
        Logger::$ProcessID =  $server->worker_pid;

        // Task进程
        if ($workerId >= $server->setting['worker_num']) {
            swoole_set_process_name("YT-Tasker");
        // worker进程
        } else {
            Pool::I();
            USE_REDIS && YRedis::I();
            swoole_set_process_name("YT-Worker");
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
     * @throws CoroutineExitException
     */
    public static function NewRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        $ctrl = new Controller($request, $response);
        $uri = Routes::Resolver($request->server['request_uri']);

        if (empty($uri['class'])) {
            $ctrl->WriteAll(null, "File Not Found", 404); return ;
        }

        try {
            $class  = "\\" . APP_NAME . "\\" . CTR_NAME . "\\{$uri['class']}";
            $handle = new $class($request, $response);

            if (!empty($uri['method']) && !method_exists($handle, $uri['method'])) {
                $ctrl->WriteAll(null, "Method Not Found: {$uri['method']}", 404); return ;
            }

            // 控制器可直接return 或者 直接WriteAll
            $response = !empty($uri['method']) ? $handle->{$uri['method']}() : "";

            // 防止控制器未继承Controller类
            if (!method_exists($handle, "WriteAll")) {
                $ctrl->WriteAll($response); return ;
            }

            // 如果已经WriteAll则无法再次WriteAll
            $ctrl->isReturn = $handle->isReturn;
            !$handle->isReturn && $handle->WriteAll($response);
        } catch (CoroutineExitException $e) {
            // end
        } catch (\ParseError $e) {
            Logger::Exception($e);
            $ctrl->WriteAll(null, "Internal Server Error", 500);
        } catch (\Exception $e) {
            Logger::Exception($e);
            $ctrl->WriteAll(null, "Internal Server Error", 500);
        } catch (\Error $e) {
            Logger::Exception($e);
            $ctrl->WriteAll(null, "Internal Server Error", 500);
        }
    }

    public static function Task(\Swoole\Server $server, $task_id, $src_worker_id, $data)
    {
        // TODO: Implement Task() method.
    }

    public static function Finish(\Swoole\Server $server, $task_id, $data)
    {
        // TODO: Implement Finish() method.
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

            echo <<<EOT
                                           
,--.   ,--. ,--. ,--. ,--------. ,--. ,--. 
 \  `.'  /  |  | |  | '--.  .--' |  | |  | 
  '.    /   |  | |  |    |  |    |  | |  | 
    |  |    '  '-'  '    |  |    '  '-'  ' 
    `--'     `-----'     `--'     `-----'  
                                                                                       
         <Yutu HTTP Server v{$v}>       
               port: {$port}       

EOT;
            return ;
        }

        Logger::Record("Server Start");

        // $server->master_pid;
        // $server->manager_pid
    }
}