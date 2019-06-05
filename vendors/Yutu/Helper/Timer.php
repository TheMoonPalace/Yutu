<?php
/**
 * Timer.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/6/3 0003
 * Time: 14:21
 */

namespace Yutu\Helper;


use Yutu\Interfaces\ICrontab;
use Yutu\Type\CrontabWorker;

class Timer
{
    /**
     * @var \Swoole\Process
     */
    private $process;

    /**
     * @var bool
     */
    private $isInit = false;

    /**
     * 任务列表
     * @var array
     */
    private $taskTable = [];

    /**
     * @var Timer
     */
    private static $instance;

    /**
     * @return Timer
     */
    public static function I()
    {
        if (empty(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return \Swoole\Process
     */
    public function Init()
    {
        if ($this->isInit) {
            return null;
        }

        $process = new \Swoole\Process(function (\Swoole\Process $process) {
            // 修改进程名
            swoole_set_process_name("YT-Soldier");

            // 装入作业
            $this->loadTask(PATH_CRONTAB, "crontab");
            $this->loadTask(CoreDI . "/Yutu/Crontab", "Yutu\Crontab");

            // 将管道加入到事件循环中，变为异步模式
            swoole_event_add($process->pipe, function () use ($process) {
                $this->exec($process->read());
            });

            // 启动定时器
            swoole_timer_tick(1000, function () {
                $this->run();
            });
        });

        $this->isInit = true;
        $this->process = $process;

        return $process;
    }

    /**
     * @param $taskName
     */
    public function Call($taskName)
    {
        $this->process->write($taskName);
    }

    // 启动定时器
    private function run()
    {
        $time = time();
        $date = date("H:i");

        foreach ($this->taskTable as $key => &$value)
        {
            // TODO 目前不允许多个窗口期重复执行
            // 也就是说假如一个定时任务  执行时间过长，长到轮到下个窗口期时还在执行上个窗口期的任务，此时当前窗口期不再执行该任务
            if ($value->exStatus || $value->inWindow) {
                continue;
            }

            switch ($value->taskType)
            {
                // 闹钟模式
                case ICrontab::TimeTask:
                    if ($value->execTime == $date)
                    {
                        $value->inWindow = true;
                        $this->exec($key);

                        // 两分钟后重置窗口期标志位
                        swoole_timer_after(120000, function () use ($value) {
                            $value->inWindow = false;
                        });
                    }

                    break;
                // 秒表模式
                case ICrontab::TickTask:
                    if ($time - $value->lastTime > $value->execTime) {
                        $this->exec($key);
                    }

                    break;
                // 手动模式
                case ICrontab::ManualTask:
                    break;
                default:
            }
        }
    }

    /**
     * @param $taskName
     */
    private function exec($taskName)
    {
        if (!isset($this->taskTable[$taskName]) || $this->taskTable[$taskName]->exStatus) {
            return ;
        }

        $task = $this->taskTable[$taskName];
        $task->exStatus = true;

        go(function () use (&$task, $taskName) {
            try {
                call_user_func($task->executor);
            } catch (\Exception $e) {
                logger::Exception($e);
            } catch (\Error $e) {
                logger::Exception($e);
            }

            $task->lastTime = time();
            $task->exStatus = false;
        });
    }

    /**
     * 加载作业文件
     * @param $path
     * @param $namespace
     * @throws \ReflectionException
     */
    private function loadTask($path, $namespace)
    {
        if (!is_dir($path)) {
            logger::ExtremelySerious("`$path` Not a directory");
        }

        foreach (scandir($path) as &$value)
        {
            if ($value == ".." || $value == ".") {
                continue;
            }

            $fileName = explode(".php", $value);

            if (count($fileName) < 2) {
                continue;
            }

            $className = "$namespace\\$fileName[0]";
            $interface = (new \ReflectionClass($className))->getInterfaceNames();

            if (!in_array("Yutu\Interfaces\ICrontab", $interface)) {
                continue;
            }

            $taskClass = new $className;
            $tabeClass = new CrontabWorker();

            $tabeClass->lastTime = 0;
            $tabeClass->inWindow = false;
            $tabeClass->exStatus = false;
            $tabeClass->execTime = $taskClass->Time();
            $tabeClass->taskType = $taskClass->Type();
            $tabeClass->executor = function () use ($taskClass) {$taskClass->Executor();};

            $this->taskTable[$className] = $tabeClass;
        }
    }

}