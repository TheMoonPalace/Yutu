<?php
/**
 * Request.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/1/31 0031
 * Time: 11:28
 */

namespace Yutu\net\http;

/**
 * Trait Request
 * @package Yutu\net\http
 */
trait Request
{
    /**
     * @var \swoole_http_request
     */
    public $request;

    /**
     * @param string $key
     * @return array|null'
     */
    public function GetHeader($key = "")
    {
        if (empty($key)) {
            return $this->request->header;
        }

        if (isset($this->request->header[$key])) {
            return $this->request->header[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function GetCookie($key = "")
    {
        if (empty($key)) {
            return $this->request->cookie;
        }

        if (isset($this->request->cookie[$key])) {
            return $this->request->cookie[$key];
        }

        return null;
    }
}