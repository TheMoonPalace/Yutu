<?php
/**
 * Controller.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 11:28
 */

namespace Yutu\net\http;

/**
 * Class Controller
 * @package Yutu\net\http
 */
class Controller
{
    use Request;
    use Response;

    /**
     * Controller constructor.
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     */
    public function __construct(\Swoole\Http\Request &$request, \Swoole\Http\Response &$response)
    {
        $this->request  = $request;
        $this->response = $response;

        $this->SetHeader("server", "Yutu");
    }
}