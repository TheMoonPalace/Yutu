<?php
/**
 * Env.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/11 0011
 * Time: 10:44
 */

namespace Yutu;


use Yutu\Helper\Logger;

class Env
{
    const YUTU_VERSION        = 0.1;                // 版本号
    const YUTU_PID_FILE       = "ytsw.pid";         // pid 文件名
    const YUTU_LOG_FILE       = "info.log";         // 日志文件名
    const YUTU_CONF_FILE      = "config.yml";       // 配置文件名

    const YUTU_SYS_HELP       = "help";             // args help
    const YUTU_SYS_STOP       = "stop";             // args stop
    const YUTU_SYS_INIT       = "init";             // args init
    const YUTU_SYS_START      = "start";            // args start
    const YUTU_SYS_RELOAD     = "reload";           // args reload
    const YUTU_SYS_RESTART    = "restart";          // args restart

    // 加载的配置文件配置
    private static $config = [];
    // flag
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
            $file = DI . "/" . self::YUTU_CONF_FILE;
            if (!file_exists($file)) return null;

            self::$config = \Spyc\Spyc::YAMLLoad($file);
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

    /**
     * 初始化运行环境
     */
    public static function RegisterYutuRuntimeEnvironment()
    {
        date_default_timezone_set("Asia/Shanghai");

        defined("CTR_NAME") or define("CTR_NAME", "controller");
        defined("PATH_APP") or define("PATH_APP", DI . "/" . APP_NAME);
        defined("PATH_CONTROLLER") or define("PATH_CONTROLLER", PATH_APP . "/" . CTR_NAME);

        defined("PATH_CRONTAB") or define("PATH_CRONTAB", DI . "/crontab");
        defined("PATH_RUNTIME") or define("PATH_RUNTIME", DI . "/runtime");
        defined("PATH_CONFIG_FILE") or define("PATH_CONFIG_FILE", DI . "/" . self::YUTU_CONF_FILE);

        defined("PATH_LOGS") or define("PATH_LOGS", PATH_RUNTIME . "/logs");
        defined("PATH_CACHE") or define("PATH_CACHE", PATH_RUNTIME . "/cache");
        defined("PATH_BACKUP") or define("PATH_BACKUP", PATH_RUNTIME . "/backup");

        !is_dir(PATH_APP) && mkdir(PATH_APP);
        !is_dir(PATH_CRONTAB) && mkdir(PATH_CRONTAB);
        !is_dir(PATH_RUNTIME) && mkdir(PATH_RUNTIME);
        !is_dir(PATH_CONTROLLER) && mkdir(PATH_CONTROLLER);

        !is_dir(PATH_LOGS) && mkdir(PATH_LOGS);
        !is_dir(PATH_CACHE) && mkdir(PATH_CACHE);
        !is_dir(PATH_BACKUP) && mkdir(PATH_BACKUP);

        !is_file(PATH_CONFIG_FILE) && file_put_contents(PATH_CONFIG_FILE, self::defaultConfigValue());
    }

    /**
     * 注册错误捕捉事件
     */
    public static function RegisterYutuRuntimeExceptionHandler()
    {
        error_reporting(0);

        // 服务器启动后不生效
        set_error_handler(function ($errNo, $errStr, $errFile, $errLine) {
            Logger::Exception(['message' => $errStr, 'file' => $errFile, 'line' => $errLine]);
        });

        // 服务器启动后不生效
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

    /**
     * 默认配置文件
     * @return string
     */
    private static function defaultConfigValue()
    {
        return <<<EOT
# ================== server config ==================
# server config https://yutu.hypot.xyz
# swoole config https://wiki.swoole.com/wiki/

# listen port
port: 8080

# worker process number
worker-num: 4

# daemonize mode
daemonize: false

# ================= database config =================
# database type
#db-type: "mysql"

# database pool number
#db-pool: 10

# table prefix
#db-pre: ""

# database port
#db-port: 3306

# database host address
#db-host: "localhost"

# database name
#db-name: ""

# database user name
#db-user: ""

# database password
#db-password: ""
EOT;
    }
}