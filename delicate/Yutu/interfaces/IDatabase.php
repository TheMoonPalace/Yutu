<?php
/**
 * IDatabase.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/2/11 0011
 * Time: 15:43
 */

namespace Yutu\interfaces;


interface IDatabase
{
    public function Query($sql = "");
}