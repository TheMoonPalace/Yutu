<?php
/**
 * Env.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 11:13
 */

namespace Yutu\moon;

use Spyc\Spyc;
use Yutu\helper\Logger;

class Env
{
    const YUTU_VERSION        = 0.01;               // 版本号
    const YUTU_PID_FILE       = "ytserver.pid";     // pid 文件名
    const YUTU_LOG_FILE       = "info.log";         // 日志文件名
    const YUTU_CONF_FILE      = "config.yml";       // 配置文件名

    const YUTU_SYS_HELP       = "help";             // args help
    const YUTU_SYS_STOP       = "stop";             // args stop
    const YUTU_SYS_INIT       = "init";             // args init
    const YUTU_SYS_START      = "start";            // args start
    const YUTU_SYS_RELOAD     = "reload";           // args reload
    const YUTU_SYS_RESTART    = "restart";          // args restart

    // 默认的配置文件内容
    private const YUTU_CONFIG_DEFAULT = <<<EOT
# server config https://yutu.aowu.io  
# swoole config https://wiki.swoole.com/wiki/
  
# listen port
#port: 8080
port: 8080
    
# work process number
#work-num: 4
work-num: 2 

# daemonize mode
#daemonize: true
daemonize: false
EOT;

    /**
     * 加载的配置文件配置
     * @var array
     */
    private static $config = [];

    /**
     * flag
     * @var bool
     */
    private static $isLoad = false;

    /**
     * @param string $key
     * @param null $default
     * @return array|mixed|null
     */
    public static function Config(string $key = "", $default = null)
    {
        if (!self::$isLoad)
        {
            $file = APP_PATH . "/" . self::YUTU_CONF_FILE;
            if (!file_exists($file)) return null;

            self::$config = Spyc::YAMLLoad($file);
            self::$isLoad = true;
        }

        if (empty($key)) {
            return self::$config;
        }

        if (isset(self::$config[$key])) {
            return self::$config[$key];
        } else {
            self::$config[$key] = $default;
        }

        return $default;
    }

    /**
     * 获取server pid
     * @return bool|false|string
     */
    public static function ServerPid()
    {
        $filePath = PATH_CACHE . "/" . self::YUTU_PID_FILE;

        if (!file_exists($filePath)) {
            return false;
        }

        return file_get_contents($filePath);
    }

    // TODO 获取manager进程pid
    public static function ManagerPid()
    {
        return null;
    }

    // 初始化运行环境
    public static function RegisterYutuRuntimeEnvironment()
    {
        date_default_timezone_set("Asia/Shanghai");

        defined("PATH_TASK") or define("PATH_TASK", APP_PATH . "/task");
        defined("PATH_MODEL") or define("PATH_MODEL", APP_PATH . "/model");
        defined("PATH_RUNTIME") or define("PATH_RUNTIME", APP_PATH . "/runtime");
        defined("PATH_CONTROLLER") or define("PATH_CONTROLLER", APP_PATH . "/controller");
        defined("PATH_CONFIG_FILE") or define("PATH_CONFIG_FILE", APP_PATH . "/" . self::YUTU_CONF_FILE);

        defined("PATH_LOGS") or define("PATH_LOGS", PATH_RUNTIME . "/logs");
        defined("PATH_CACHE") or define("PATH_CACHE", PATH_RUNTIME . "/cache");
        defined("PATH_BACKUP") or define("PATH_BACKUP", PATH_RUNTIME . "/backup");

        !is_dir(APP_PATH) && mkdir(APP_PATH);
        !is_dir(PATH_TASK) && mkdir(PATH_TASK);
        !is_dir(PATH_MODEL) && mkdir(PATH_MODEL);
        !is_dir(PATH_RUNTIME) && mkdir(PATH_RUNTIME);
        !is_dir(PATH_CONTROLLER) && mkdir(PATH_CONTROLLER);

        !is_dir(PATH_LOGS) && mkdir(PATH_LOGS);
        !is_dir(PATH_CACHE) && mkdir(PATH_CACHE);
        !is_dir(PATH_BACKUP) && mkdir(PATH_BACKUP);

        !is_file(PATH_CONFIG_FILE) && file_put_contents(PATH_CONFIG_FILE, self::YUTU_CONFIG_DEFAULT);
    }

    // 注册错误捕捉
    public static function RegisterYutuRuntimeExceptionHandler()
    {
        error_reporting(0);

        // swoole下不生效
        set_error_handler(function ($errNo, $errStr, $errFile, $errLine) {
            Logger::Exception(['message' => $errStr, 'file' => $errFile, 'line' => $errLine]);
        });

        // swoole下不生效
        set_exception_handler(function ($e) {
            Logger::Exception($e);
        });

        // 这个可以
        register_shutdown_function(function () {
            $e = error_get_last();

            if (!is_null($e)) {
                Logger::Exception($e);
            }
        });
    }
}