<?php
/**
 * IDatabase.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/2/11 0011
 * Time: 15:43
 */

namespace Yutu\interfaces;

/**
 * Interface IDatabase
 * @package Yutu\interfaces
 */
interface IDatabase
{
    /**
     * @param $sql
     * @param array $prepare
     * @param bool $fetch
     * @return mixed
     */
    public function Query($sql, $prepare = [], $fetch = false);

    /**
     * @param $sql
     * @param array $prepare
     * @return mixed
     */
    public function Execute($sql, $prepare = []);

    /**
     * @return mixed
     */
    public function Commit();

    /**
     * @return mixed
     */
    public function RollBack();

    /**
     * @return mixed
     */
    public function BeginTransaction();
}