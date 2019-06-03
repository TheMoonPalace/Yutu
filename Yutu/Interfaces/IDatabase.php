<?php
/**
 * IDatabases.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/20 0020
 * Time: 9:56
 */

namespace Yutu\Interfaces;


interface IDatabase
{
    public function BeginTransaction();

    public function RollBack();

    public function Commit();

    public function Query(string $sql, array $prepare = [], bool $fetch = false);

    public function Execute(string $sql, array $prepare = []);

    public function Save(string $table, array $set = []);

    public function Update(string $table, array $set, $where);

    public function Select(string $table, $condition = [], $other = [], bool $returnSql = false);

    public function Delete(string $table, $condition);

}