<?php
/**
 * ISchedule.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/6/3 0003
 * Time: 14:20
 */

namespace Yutu\Interfaces;


interface ICrontab
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