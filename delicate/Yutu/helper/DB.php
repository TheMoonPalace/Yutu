<?php
/**
 * Db.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/2/11 0011
 * Time: 10:45
 */

namespace Yutu\helper;


use Yutu\database\Pool;

/**
 * Class DB
 * @package Yutu\helper
 */
class DB
{
    /**
     * @param $sql
     * @return string
     */
    public static function Select($sql)
    {
        return Pool::I()->Call("Query", $sql);
    }

}