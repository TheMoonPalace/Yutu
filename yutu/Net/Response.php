<?php
/**
 * Response.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/12 0012
 * Time: 10:58
 */

namespace Yutu\Net;


trait Response
{
    /**
     * @var \Swoole\Http\Response
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
    public function Write($data, $msg = '', $code = 200)
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
    public function WriteAll($data, $msg = '', $code = 200)
    {
        $data = $this->format($code, $data, $msg);
        $this->response->end($data);
        $this->isReturn = true;
    }

    /**
     * $this->response->sendfile()
     * @param $fileName
     */
    public function SendFile($fileName)
    {
        $this->response->sendfile($fileName);
    }

    /**
     * $this->response->cookie()
     * @param $key
     * @param $value
     */
    public function SetCookie($key, $value)
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
    public function SetHeader($key, $value)
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
    public function SetStatus($code)
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
        return json_encode(['code' => $code, 'info' => $message, 'data' => $data]);
    }
}