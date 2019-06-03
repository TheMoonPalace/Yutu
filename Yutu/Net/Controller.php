<?php
/**
 * Controller.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/12 0012
 * Time: 10:58
 */

namespace Yutu\Net;


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