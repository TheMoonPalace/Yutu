<?php
/**
 * DB.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/20 0020
 * Time: 9:37
 */

namespace Yutu\helper;


use Yutu\database\Pool;
use Yutu\interfaces\IDatabases;

class DB implements IDatabases
{
    /**
     * @var DB
     */
    private static $instance;

    /**
     * DB constructor.
     */
    private function __construct()
    {
    }

    /**
     * @return DB
     */
    public static function I()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param $table
     * @param array $condition
     * @param array $other
     * @param bool $returnSql
     * @return null
     */
    public function Select($table, $condition = [], $other = [], $returnSql = false)
    {
        return Pool::I()->Call("Select", $table, $condition, $other, $returnSql);
    }

    public function Query($sql)
    {

    }
}