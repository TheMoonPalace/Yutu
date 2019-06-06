<?php
/**
 * Mysql.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/20 0020
 * Time: 9:40
 */

namespace Yutu\Database;


use Yutu\Env;
use Yutu\Helper\Logger;
use Yutu\Interfaces\IDatabase;

class Mysql implements IDatabase
{
    /**
     * @var \Swoole\Coroutine\MySQL
     */
    private $link = null;

    /**
     * 数据表前缀
     * @var string
     */
    private $prefix = "";

    /**
     * Mysql constructor.
     * @param $link
     * @throws \Exception
     */
    public function __construct($link)
    {
        if (empty($link)) {
            throw (new \Exception("empty database link"));
        }

        $this->link = $link;
        $this->prefix = Env::Config("db-pre", "");
    }

    public function BeginTransaction()
    {
        $this->link->begin();
    }

    public function RollBack()
    {
        $this->link->rollback();
    }

    public function Commit()
    {
        $this->link->commit();
    }

    /**
     * @param $sql
     * @param array $prepare
     * @param bool $fetch
     * @return array|bool
     */
    public function Query(string $sql, array $prepare = [], bool $fetch = false)
    {
        try {
            $stat = $this->link->prepare($sql);

            if (!$stat) {
                $this->error($this->link->error, $sql); return [];
            }

            $exec = $stat->execute($prepare);

            if ($exec !== false) {
                return $fetch && !empty($exec) ? $exec[0] : $exec;
            }

            $this->error($stat->error, $sql);
        } catch (\Exception $e) {
            $this->error($e, $sql);
        } catch (\Error $e) {
            $this->error($e, $sql);
        }

        return [];
    }

    /**
     * @param string $sql
     * @param array $prepare
     * @return int
     */
    public function Execute(string $sql, array $prepare = [])
    {
        try {
            $stat = $this->link->prepare($sql);

            if (!$stat) {
                $this->error($this->link->error, $sql); return 0;
            }

            $exec = $stat->execute($prepare);

            if ($exec !== false) {
                return $stat->affected_rows;
            }

            $this->error($stat->error, $sql);
        } catch (\Exception $e) {
            $this->error($e, $sql);
        } catch (\Error $e) {
            $this->error($e, $sql);
        }

        return 0;
    }

    /**
     * @param string $table
     * @param array $set
     * @return bool|int
     */
    public function Save(string $table, array $set = [])
    {
        if (empty($table) || empty($set)) {
            return false;
        }

        $set = $this->buildSet($set);
        $sql = "INSERT INTO {$table} SET {$set['s']}";

        try {
            $stat = $this->link->prepare($sql);

            if (!$stat) {
                $this->error($this->link->error, $sql); return 0;
            }

            $exec = $stat->execute($set['p']);

            if ($exec !== false) {
                return $stat->insert_id;
            }

            $this->error($stat->error, $sql);
        } catch (\Exception $e) {
            $this->error($e, $sql);
        } catch (\Error $e) {
            $this->error($e, $sql);
        }

        return 0;
    }

    /**
     * @param string $table
     * @param array $set
     * @param $where
     * @return bool
     */
    public function Update(string $table, array $set, $where)
    {
        if (empty($table) || empty($set) || empty($where)) {
            return false;
        }

        $set = $this->buildSet($set);
        $condition = $this->buildWhere($where);
        $sql = "UPDATE {$table} SET {$set['s']} WHERE {$condition['c']}";

        try {
            $stat = $this->link->prepare($sql);

            if (!$stat) {
                $this->error($this->link->error, $sql); return 0;
            }

            $exec = $stat->execute(array_merge($set['p'], $condition['p']));

            if ($exec !== false) {
                return true;
            }

            $this->error($stat->error, $sql);
        } catch (\Exception $e) {
            $this->error($e, $sql);
        } catch (\Error $e) {
            $this->error($e, $sql);
        }

        return false;
    }

    /**
     * @param string $table
     * @param array $condition
     * @param array $other
     * @param bool $returnSql
     * @return array|bool
     */
    public function Select(string $table, $condition = [], $other = [], bool $returnSql = false)
    {
        if (empty($table)) {
            return [];
        }

        $condition = $this->buildWhere($condition);
        $where = !empty($condition['c']) ? " WHERE " . $condition['c'] : "";

        $field = isset($other['field']) ? $other['field'] : '*';
        $fetch = isset($other['fetch']) ? $other['fetch'] : false;

        $join  = isset($other['join']) ? " LEFT JOIN " . $other['join'] : '';
        $order = isset($other['order']) ? " ORDER BY {$other['order']}" : "";
        $limit = isset($other['limit']) ? " LIMIT {$other['limit']}" : "";

        $sql = "SELECT {$field} FROM {$table}{$join}{$where}{$order}{$limit}";

        if ($returnSql) {
            return [$sql];
        }

        try {
            $stat = $this->link->prepare($sql);

            if (!$stat) {
                $this->error($this->link->error, $sql); return [];
            }

            $exec = $stat->execute($condition['p']);

            if ($exec !== false) {
                return $fetch && !empty($exec) ? $exec[0] : $exec;
            }

            $this->error($stat->error, $sql);
        } catch (\Exception $e) {
            $this->error($e, $sql);
        } catch (\Error $e) {
            $this->error($e, $sql);
        }

        return [];
    }

    /**
     * @param string $table
     * @param $condition
     * @return bool|int
     */
    public function Delete(string $table, $condition)
    {
        if (empty($table) || empty($condition)) {
            return false;
        }

        $where = $this->buildWhere($condition);
        $sql = "DELETE FROM {$table} WHERE {$where['c']}";

        try {
            $stat = $this->link->prepare($sql);

            if (!$stat) {
                $this->error($this->link->error, $sql); return 0;
            }

            $exec = $stat->execute($where['p']);

            if ($exec !== false) {
                return $stat->affected_rows;
            }

            $this->error($stat->error, $sql);
        } catch (\Exception $e) {
            $this->error($e, $sql);
        } catch (\Error $e) {
            $this->error($e, $sql);
        }

        return 0;
    }

    /**
     * @param array $array
     * @return array
     */
    protected function buildSet($array = [])
    {
        $set = '';
        $prepare_data = [];

        if (!is_array($array)) {
            return ['s' => $array, 'p' => $prepare_data];
        }

        foreach ($array as $key => $value)
        {
            if (is_array($value)) {
                $set .= "$key {$value[0]} " . addslashes($value[1]) . ',';
            } else {
                $set .=  "{$key}=?,";
                $prepare_data["_{$key}"] = addslashes($value);
            }
        }

        $set = trim($set, ',');
        return ['s' => $set, 'p' => $prepare_data];
    }

    /**
     * @param array $array
     * @return array
     */
    protected function buildWhere($array = [])
    {
        $condition = '';
        $prepare_data = [];

        if (!is_array($array)) {
            return ['c' => $condition, 'p' => $prepare_data];
        }

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                if (gettype($value[1]) == "string") {
                    $condition .= "$key {$value[0]} '$value[1]'";
                } else {
                    $condition .= "$key {$value[0]} $value[1]";
                }
            }
            else
            {
                $k = str_replace(".", "", $key);
                $condition .= "$key=?";
                $prepare_data["_{$k}_"] = $value;
            }

            $condition .= ' AND ';
        }

        return ['c' => trim($condition, ' AND '), 'p' => $prepare_data];
    }

    /**
     * @param $e
     * @param $sql
     */
    private function error($e, $sql)
    {
        if (is_object($e)) {
            Logger::Exception("[SQL]:" . var_export($e->getMessage()) . "; " . $sql . "\n" );
        } else {
            Logger::Exception("[SQL]:" . var_export($e, true) . "; " . $sql . "\n");
        }
    }

}