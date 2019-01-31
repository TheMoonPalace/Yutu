[中文](./README.md) | English

Yutu
====
*The Based Swoole Lightweight PHP-HTTP Server Framework*
![](./moon/yutu.png)

### Installation
- Linux, OS X, Windows Subsystem for Linux
- Swoole4.2.12+
- PHP7.1+
```git
$ git cloen https://github.com/TheMoonPalace/Yutu-swoole.git
```
```sybase
$ curl 'https://github.com/TheMoonPalace/Yutu-swoole/archive/...'
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
        // OR $this->writeAll("Welcome To The Moon Palace");
        return "Welcome To The Moon Palace";
    }

}
```
run app: 
```
$ ./yutu start [app name]
```

### Config
configuration files：your app/config.yml
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