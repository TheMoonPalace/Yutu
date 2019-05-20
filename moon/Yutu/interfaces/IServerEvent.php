<?php
/**
 * IServerEvent.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/12 0012
 * Time: 10:48
 */

namespace Yutu\interfaces;


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
     * @param \Swoole\Server $server
     * @param $task_id
     * @param $src_worker_id
     * @param $data
     * @return mixed
     */
    public static function Task(\Swoole\Server $server, $task_id, $src_worker_id,  $data);

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