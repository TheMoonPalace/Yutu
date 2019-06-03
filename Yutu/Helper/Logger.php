<?php
/**
 * Logger.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/11 0011
 * Time: 13:29
 */

namespace Yutu\Helper;


use Yutu\YutuSw;

class Logger
{
    /**
     * 当前进程ID
     * @var int
     */
    public static $ProcessID = -1;

    /**
     * @param string $message
     */
    public static function Record($message = '')
    {
        self::Write("info", $message);
    }

    /**
     * @param null $e
     */
    public static function Exception($e = null)
    {
        if (is_null($e)) {
            return;
        }

        if (is_object($e)) {
            $e = $e->getMessage() . ";file:" . $e->getFile() . ";line:" . $e->getLine();
        } else if (is_array($e)) {
            $e = $e['message'] . ";file:" . $e['file'] . ";line:" . $e['line'];
        }

        self::Write("error", $e);
    }

    /**
     * 记录日志，非后台模式下无法写入info.log
     * @param $type
     * @param string $message
     */
    public static function Write($type, $message = '')
    {
        $msg = "[" . date("Y-m-d H:i:s") . " $" . self::$ProcessID . " $type] " . $message . "\n";
        echo $msg;
    }

    /**
     * 禁止在服务器完全启动后使用
     * @param null $e
     * @param bool $stop
     */
    public static function ExtremelySerious($e = null, $stop = true)
    {
        self::Exception($e); $stop && YutuSw::I()->StopHTTPServer(); exit;
    }
}