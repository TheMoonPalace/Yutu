<?php
/**
 * IDatabases.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/20 0020
 * Time: 9:56
 */

namespace Yutu\interfaces;


interface IDatabases
{
    public function Select($table, $condition = [], $other = [], $returnSql = false);

    public function Query($sql);
}