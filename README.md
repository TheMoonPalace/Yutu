中文 | [English](./README-EN.md)

Yutu
====
*基于Swoole简单易上手的轻量级Api-PHP-HTTP服务器框架*
![](./moon/yutu.png)

### Installation
- Linux, OS X, Windows Subsystem for Linux
- Swoole4.2.12+
- PHP7.1+
```git
$ git cloen https://github.com/TheMoonPalace/Yutu.git
```
```sybase
$ curl 'https://github.com/TheMoonPalace/Yutu/archive/...'
```

### Quick start
```sybase
$ ./yutu init
```
example:
```php
<?php
namespace app\controller;

use Yutu\net\http;

class api extends http\controller
{
    public function get()
    {
        // OR $this->WriteAll("Welcome To The Moon Palace");
        return "Welcome To The Moon Palace";
    }

}
```
run app: 
```
$ ./yutu start [app name]
```

### Config
下载完后可以进行项目初始化 run ./yutu init, 框架会自动生成配置文件：your app/config.yml
```yaml
# server port
#port: 8080

# daemonize mode
#daemonize: false

# worker process number
#work-num:2
```

### TODO
- [ ] ......