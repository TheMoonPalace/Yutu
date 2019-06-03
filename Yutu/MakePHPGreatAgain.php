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
     * @param array $args
     */
    public function __construct(string $root, array $args)
    {
        global $argv;
        global $argc;

        if ($argc < 2) {
            $this->help();
        }

        if (version_compare(PHP_VERSION, "7.1", "<")) {
            exit("PHP version is too low, need 7.1+\n");
        }

        if (version_compare(SWOOLE_VERSION, "4.2.12", "<")) {
            exit("Swoole version is too low, need 4.2.12+\n");
        }

        define("DI", $root);
        define("APP_NAME", isset($args['name']) ? $args['name'] : "App");

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
    init         *initialization app
    start        *start server
    restart      *restart server
    reload       *reload server
    stop         *stop server   


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