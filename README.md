中文 | [English](./README-EN.md)

Yutu
====
*基于Swoole简单易上手的轻量级PHP-Api服务框架*
![](./logo.png)

### 环境
- Linux, OS X, Windows Subsystem for Linux
- Swoole4.2.12+
- PHP7.1+

### 运行
```sybase
$ ./yserv init
```
example:
```php
<?php
namespace app\controller;


class api 
{
    public function get()
    {
        // OR
        // extends Yutu\Net\Controller
        // $this->WriteAll("Welcome To The Moon Palace");
        return "Welcome To The Moon Palace";
    }

}
```
run app: 
```
$ ./yserv start
```

### 配置
下载完后可以进行项目初始化 run ./yserv init, 框架会自动生成配置文件：config.yml
```yaml
# server port
#port: 8080

# daemonize mode
#daemonize: false

# worker process number
#work-num:2
```