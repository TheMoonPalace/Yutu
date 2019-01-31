<?php
/**
 * ITask.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 11:24
 */

namespace Yutu\interfaces;


interface ITask
{
// 闹钟模式定时任务
    const TimeTask   = 1;

    // 秒表模式定时任务
    const TickTask   = 2;

    // 手动触发任务
    const ManualTask = 3;

    /**
     * 获取任务类型
     * @return mixed
     */
    public function Type();

    /**
     * 获取任务执行时间
     * @return mixed | 1 or 12:00
     */
    public function Time();

    /**
     * 执行单元
     * @return mixed
     */
    public function Executor();
}