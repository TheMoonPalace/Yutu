<?php
/**
 * DB.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/20 0020
 * Time: 9:37
 */

namespace Yutu\Helper;


use Yutu\Database\Pool;
use Yutu\Env;
use Yutu\Interfaces\IDatabase;

class DB implements IDatabase
{
    /**
     * 数据表前缀
     * @var string
     */
    private $prefix = "";

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
        if (empty(self::$instance))
        {
            self::$instance = new self();
            self::$instance->prefix = Env::Config("db-pre", "");
        }

        return self::$instance;
    }

    public function BeginTransaction()
    {
        Pool::I()->Call(__FUNCTION__);
    }

    public function RollBack()
    {
        Pool::I()->Call(__FUNCTION__);
    }

    public function Commit()
    {
        Pool::I()->Call(__FUNCTION__);
    }

    /**
     * @param string $sql
     * @param array $prepare
     * @param bool $fetch
     * @return null
     */
    public function Query(string $sql, array $prepare = [], bool $fetch = false)
    {
        return Pool::I()->Call(__FUNCTION__, $sql, $prepare, $fetch);
    }

    /**
     * @param string $sql
     * @param array $prepare
     * @return null
     */
    public function Execute(string $sql, array $prepare = [])
    {
        return Pool::I()->Call(__FUNCTION__, $sql, $prepare);
    }

    /**
     * @param string $table
     * @param array $set
     * @return null
     */
    public function Save(string $table, array $set = [])
    {
        $table = $this->prefix . $table;
        return Pool::I()->Call(__FUNCTION__, $table, $set);
    }

    /**
     * @param string $table
     * @param array $set
     * @param $where
     * @return null
     */
    public function Update(string $table, array $set, $where)
    {
        $table = $this->prefix . $table;
        return Pool::I()->Call(__FUNCTION__, $table, $set, $where);
    }

    /**
     * @param string $table
     * @param array $condition
     * @param array $other
     * @param bool $returnSql
     * @return null
     */
    public function Select(string $table, $condition = [], $other = [], bool $returnSql = false)
    {
        $table = $this->prefix . $table;
        return Pool::I()->Call(__FUNCTION__, $table, $condition, $other, $returnSql);
    }

    /**
     * @param string $table
     * @param $condition
     * @return null
     */
    public function Delete(string $table, $condition)
    {
        $table = $this->prefix . $table;
        return Pool::I()->Call(__FUNCTION__, $table, $condition);
    }

    /**
     * @param string $table
     * @param array $condition
     * @param array $other
     * @param bool $returnSql
     * @return null
     */
    public function Fetch(string $table, $condition = [], $other = [], $returnSql = false)
    {
        $other['fetch'] = true;
        $table = $this->prefix . $table;

        return Pool::I()->Call("Select", $table, $condition, $other, $returnSql);
    }

    /**
     * @param string $table
     * @param array $condition
     * @return int
     */
    public function Count(string $table, $condition = [])
    {
        $table  = $this->prefix . $table;
        $result = Pool::I()->Call("Select", $table, $condition, ['field' => 'count(*) as c', 'fetch' => true]);

        return isset($result['c']) ? $result['c'] : 0;
    }

    /**
     * 增加字段值
     * @param $table
     * @param $field
     * @param array $condition
     * @return bool|int
     */
    public function IncValue(string $table, $field, $condition = [])
    {
        $table  = $this->prefix . $table;
        $field  = "`$field` = `$field` + 1";

        return Pool::I()->Call("Update", $table, $field, $condition);
    }

    /**
     * 减少字段值
     * @param $table
     * @param $field
     * @param array $condition
     * @return bool|int
     */
    public function DecValue($table, $field, $condition = [])
    {
        $table  = $this->prefix . $table;
        $field  = "`$field` = `$field` - 1";

        return Pool::I()->Call("Update", $table, $field, $condition);
    }

}