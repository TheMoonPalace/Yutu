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
     * @return array|null
     * @throws YutuDBException
     */
    public function Query($sql = "")
    {
        $result = $this->link->query($sql);

        if ($result) {
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        }

        if (!empty($this->link->errorInfo()[1])) {
            throw new YutuDBException($this->link->errorInfo()[2], $this->link->errorInfo()[1]);
        }

        return null;
    }

}