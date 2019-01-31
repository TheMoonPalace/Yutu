<?php
/**
 * Response.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 11:29
 */

namespace Yutu\net\http;


trait Response
{
    /**
     * @var \swoole_http_response
     */
    public $response;

    /**
     * @var bool
     */
    public $isReturn = false;

    /**
     * $this->response->write()
     * @param $data
     * @param string $msg
     * @param int $code
     */
    public function write($data, $msg = '', $code = 200)
    {
        $data = $this->format($code, $data, $msg);
        $this->response->write($data);
    }

    /**
     * $this->response->end()
     * @param $data
     * @param string $msg
     * @param int $code
     */
    public function writeAll($data, $msg = '', $code = 200)
    {
        $data = $this->format($code, $data, $msg);
        $this->response->end($data);
        $this->isReturn = true;
    }

    /**
     * $this->response->sendfile()
     * @param $fileName
     */
    public function sendFile($fileName)
    {
        $this->response->sendfile($fileName);
    }

    /**
     * $this->response->cookie()
     * @param $key
     * @param $value
     */
    public function setCookie($key, $value)
    {
        if (empty($key)) {
            return ;
        }

        $this->response->cookie($key, $value);
    }

    /**
     * $this->response->header()
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value)
    {
        if (empty($key)) {
            return ;
        }

        $this->response->header($key, $value);
    }

    /**
     * $this->response->status()
     * @param $code
     */
    public function setStatus($code)
    {
        $this->response->status($code);
    }

    /**
     * 格式化数据
     * @param $code
     * @param $data
     * @param string $message
     * @return false|string
     */
    private function format($code, $data, $message = '')
    {
        $this->setHeader("Content-Type", "application/json");
        return json_encode(['status' => $code, 'message' => $message, 'data' => $data]);
    }
}