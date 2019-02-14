<?php
/**
 * Db.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/2/11 0011
 * Time: 10:45
 */

namespace Yutu\helper;


use Yutu\database\Pool;

class DB
{
    /**
     *
     */
    public static function Query()
    {
        return Pool::I()->Call(__FUNCTION__, "select * from user");
    }


}