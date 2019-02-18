<?php
/**
 * Mysql.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/2/11 0011
 * Time: 10:33
 */

namespace Yutu\database;


use Yutu\interfaces\IDatabase;
use Yutu\types\YutuDBException;

class Mysql implements IDatabase
{
    /**
     * @var \PDO
     */
    private $link = null;

    /**
     * Mysql constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->link = $pdo;
    }

    /**
     * @param string $sql
     * @param array $prepare
     * @param bool $fetch
     * @return array|mixed|null
     * @throws YutuDBException
     */
    public function Query($sql, $prepare = [], $fetch = false)
    {
        $stat = $this->link->prepare($sql);
        $exec = $stat->execute($prepare);

        if ($exec) {
            return $fetch ? $stat->fetch(\PDO::FETCH_ASSOC) : $stat->fetchAll(\PDO::FETCH_ASSOC);
        }

        if (!empty($this->link->errorInfo()[1])) {
            throw new YutuDBException($this->link->errorInfo()[2], $this->link->errorInfo()[1]);
        }

        return null;
    }

    /**
     * @param $sql
     * @param array $prepare
     * @return int
     * @throws YutuDBException
     */
    public function Execute($sql, $prepare = [])
    {
        $stat = $this->link->prepare($sql);
        $exec = $stat->execute($prepare);

        if ($exec) {
            return $stat->rowCount();
        }

        if (!empty($this->link->errorInfo()[1])) {
            throw new YutuDBException($this->link->errorInfo()[2], $this->link->errorInfo()[1]);
        }

        return 0;
    }

    // commit
    public function Commit()
    {
        if ($this->link->inTransaction()) {
            return $this->link->commit();
        }

        return false;
    }

    // rollBack
    public function RollBack()
    {
        if ($this->link->inTransaction()) {
            return $this->link->rollBack();
        }

        return false;
    }

    // beginTransaction
    public function BeginTransaction()
    {
        return $this->link->beginTransaction();
    }

    /**
     * @param array $array
     * @return array
     */
    private function buildSet($array = [])
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
    private function buildWhere($array = [])
    {
        $condition = '';
        $prepare_data = [];

        if (!is_array($array)) {
            $condition = $array;
        } else {
            foreach ($array as $key => $value)
            {
                if (is_array($value)) {
                    $condition .= "$key {$value[0]} " . $value[1];
                } else {
                    $k = str_replace(".", "", $key);
                    $condition .= "$key=:_{$k}_";
                    $prepare_data["_{$k}_"] = $value;
                }
                $condition .= ' AND ';
            }

            $condition = trim($condition, ' AND ');
        }

        return ['c' => $condition, 'p' => $prepare_data];
    }

}