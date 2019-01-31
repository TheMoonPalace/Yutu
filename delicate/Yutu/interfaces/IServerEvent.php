<?php
/**
 * IServerEvent.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 14:40
 */

namespace Yutu\interfaces;

/**
 * Interface IServerEvent
 * @package Yutu\interfaces
 */
interface IServerEvent
{
    /**
     * on manager start
     * @param \Swoole\Server $server
     * @return mixed
     */
    public static function ManagerStart(\Swoole\Server $server);

    /**
     * on manager stop
     * @return mixed
     */
    public static function ManagerStop();

    /**
     * on worker start
     * @param \Swoole\Server $server
     * @param $workerId
     * @return mixed
     */
    public static function WorkerStart(\Swoole\Server $server, $workerId);

    /**
     * on worker error
     * @param \Swoole\Server $server
     * @return mixed
     */
    public static function WorkerError(\Swoole\Server $server);

    /**
     * on new request
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     * @return mixed
     */
    public static function NewRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response);

    /**
     * on task
     * 此处开启了task_enable_coroutine，所以只有两个参数(swoole4.2.12以上版本可用)
     * @param \Swoole\Server $server
     * @param \Swoole\Server\Task $task
     * //来自哪个`Worker`进程
     * $task->workerId;
     * //任务的编号
     * $task->id;
     * //任务的类型，taskwait, task, taskCo, taskWaitMulti 可能使用不同的 flags
     * $task->flags;
     * //任务的数据
     * $task->data;
     * //完成任务，结束并返回数据
     * $task->finish([123, 'hello']);
     * @return mixed
     */
    public static function Task(\Swoole\Server $server, \Swoole\Server\Task $task);

    /**
     * on task finish
     * @param \Swoole\Server $server
     * @param $task_id
     * @param $data
     * @return mixed
     */
    public static function Finish(\Swoole\Server $server, $task_id, $data);

    /**
     * on start
     * @param \Swoole\Server $server
     * @return mixed
     */
    public static function Start(\Swoole\Server $server);
}