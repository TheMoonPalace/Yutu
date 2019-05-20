<?php
/**
 * Mysql.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/20 0020
 * Time: 9:40
 */

namespace Yutu\database;


use Yutu\Env;
use Yutu\helper\Logger;
use Yutu\interfaces\IDatabases;

class Mysql implements IDatabases
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

    public function Query($sql)
    {
        // TODO: Implement Query() method.
    }

    /**
     * @param $table
     * @param array $condition
     * @param array $other
     * @param bool $returnSql
     * @return array|bool
     */
    public function Select($table, $condition = [], $other = [], $returnSql = false)
    {
        if (empty($table)) {
            return [];
        }

        $table = $this->prefix . $table;
        $condition = $this->buildWhere($condition);

        $field = isset($other['field']) ? $other['field'] : '*';
        $join  = isset($other['join']) ? " LEFT JOIN " . $other['join'] : '';

        $sql = "SELECT {$field} FROM {$table}{$join}";

        if (!empty($condition['c'])) {
            $sql .= " WHERE " . $condition['c'];
        }

        if (isset($other['order'])) {
            $sql .= " ORDER BY {$other['order']}";
        }

        if (isset($other['limit'])) {
            $sql .= " LIMIT {$other['limit']}";
        }

        if ($returnSql) {
            return [$sql];
        }

        $stat = $this->link->prepare($sql);

        if (!$stat) {
            Logger::Exception("[SQL]" . $sql . "\n" . var_export($this->link, true));
        }

        $exec = $stat->execute($condition['p']);

        if (!$exec) {
            Logger::Exception("[SQL]" . $sql . "\n" . $stat->error);
        }

        return $exec;
    }

    /**
     * @param array $array
     * @return array
     */
    protected function buildSet($array = [])
    {
        $set = '';
        $prepare_data = [];

        foreach ($array as $key => $value)
        {
            if (is_array($value)) {
                $set .= "$key {$value[0]} " . addslashes($value[1]) . ',';
            } else {
                $set .=  "{$key}=:_{$key},";
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
                $condition .= "$key=:_{$k}_";
                $prepare_data["_{$k}_"] = $value;
            }

            $condition .= ' AND ';
        }

        return ['c' => trim($condition, ' AND '), 'p' => $prepare_data];
    }
}