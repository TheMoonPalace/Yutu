<?php
/**
 * CrontabWork.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/6/3 0003
 * Time: 14:41
 */

namespace Yutu\Type;


class CrontabWorker
{
    /**
     * 执行类型
     * @var int
     */
    public $taskType;

    /**
     * 执行时间 - 时间戳
     * @var string
     */
    public $execTime;

    /**
     * 上次执行时间 - 时间戳
     * @var string
     */
    public $lastTime;

    /**
     * 当前是否在执行
     * @var boolean
     */
    public $exStatus;

    /**
     * 当前是否在时间窗口期中
     * @var
     */
    public $inWindow;

    /**
     * 执行体
     * @var callable
     */
    public $executor;
}