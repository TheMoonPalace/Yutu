[中文](./README.md) | English

Yutu
====
*The Based Swoole Lightweight PHP-Api Server Framework*
![](./moon/yutu.png)

### Installation
- Linux, OS X, Windows Subsystem for Linux
- Swoole4.2.12+
- PHP7.1+
```git
$ git cloen https://github.com/TheMoonPalace/Yutu.git
```

### Quick start
```sybase
$ ./yutu init
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
        // extends Yutu\net\Controller
        // $this->WriteAll("Welcome To The Moon Palace");
        return "Welcome To The Moon Palace";
    }

}
```
run app: 
```
$ ./yutu start [app name]
```

### Config
Initialization configuration file：run ./yutu init
```yaml
# server port
#port: 8080

# daemonize mode
#daemonize: false

# worker process number
#work-num:2
```