<?php
/**
 * MakePHPGreatAgain.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/10 0010
 * Time: 16:25
 */

use Yutu\Env;
use Yutu\YutuSw;

class MakePHPGreatAgain
{
    /**
     * MakePHPGreatAgain constructor.
     * @param string $root
     */
    public function __construct(string $root)
    {
        global $argv;
        global $argc;

        if ($argc < 2) $this->help();
        $appName = isset($argv[2]) && !empty($argv[2]) ? $argv[2] : "app";

        defined("DI") or define("DI", $root);
        defined("APP_NAME") or define("APP_NAME", $appName);
        defined("APP_PATH") or define("APP_PATH", DI . "/" . $appName);

        $this->autoload(__DIR__);
        Env::RegisterYutuRuntimeEnvironment();
        Env::RegisterYutuRuntimeExceptionHandler();

        switch ($argv[1])
        {
            // 初始化
            case Env::YUTU_SYS_INIT:
                break;
            // 停止服务
            case Env::YUTU_SYS_STOP:
                YutuSw::I()->StopHTTPServer(); break;
            // 启动服务
            case Env::YUTU_SYS_START:
                YutuSw::I()->CreateHTTPServer(); break;
            // 冷重启服务
            case Env::YUTU_SYS_RESTART:
                YutuSw::I()->RestartHTTPServer(); break;
            // 热重启服务
            case Env::YUTU_SYS_RELOAD:
                YutuSw::I()->ReloadHTTPServer(); break;
            // help
            case Env::YUTU_SYS_HELP:
                $this->help(); break;
            default:
                $this->help($argv[1]);
        }
    }

    /**
     * @param string $undefined
     */
    private function help($undefined = "")
    {
        if (!empty($undefined))
        {
            echo <<<EOT
Yutu: unknown subcommand {$undefined}
Run 'yutu help' for usage.

EOT;
        }
        else
        {
            echo <<<EOT
Usage:
    yutu command
    
The commands are:
    init    [app name]     *initialization app
    start   [app name]     *start server
    restart [app name]     *restart server
    reload  [app name]     *reload server
    stop    [app name]     *stop server   


EOT;
        }

        exit;
    }

    /**
     * 自动加载
     * @param string $path
     */
    private function autoload(string $path)
    {
        // 自动加载
        spl_autoload_register(function ($class) use ($path) {
            $classPath = $path . '/' . str_replace('\\', '/' , $class) . ".php";

            if (!file_exists($classPath)) {
                $classPath = DI . '/' . str_replace('\\', '/', $class) . ".php";
            }

            if (!file_exists($classPath)) {
                throw new \Exception("File Not Found: $classPath");
            }

            file_exists($classPath) && require_once $classPath;
        });
    }
}