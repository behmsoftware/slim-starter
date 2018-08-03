<?php
/**
 * Created by PhpStorm.
 * User: behme
 * Date: 02.06.2018
 * Time: 16:47
 */

namespace Slim\Helper;
use PDO;

/**
 * Class Database
 */
class Database
{

    /**
     * @var PDO
     */
    private $connection;

    /**
     * Database constructor.
     *
     * @param string $host
     * @param string $dbname
     * @param string $dbuser
     * @param string $dbpassword
     */
    public function __construct(PDO $db)
    {
        $this->connection = $db;
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * @param string $table
     * @param array $fields
     *
     * @return int
     */
    public function create(string $table, $fields = []) : int
    {
        $fieldsArr = [];
        $values = [];
        foreach ($fields as $key => $value) {
            $fieldsArr[] = $key;
            ($value[0] == 'C') ? $valueStr = '"' . $value[1] . '"' : $valueStr = $value[1];
            $values[] = $valueStr;
        }

        $fieldsStr = '(' . implode(',', $fieldsArr) . ')';
        $valuesStr = '(' . implode(',', $values) . ')';

        $sql = 'INSERT INTO ' .$table . ' ' . $fieldsStr . ' VALUES ' . $valuesStr ;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $this->connection->lastInsertId();
    }

    /**
     * Read entries from table.
     *
     * @param string $table
     * @param array $fields
     * @param array $where
     * @return array|bool|PDOStatement
     */
    public function read(string $table, $fields = [], $where = []) {
        $fieldStr = '*';
        if (!empty($fields)) {
            $fieldStr = implode(',', $fields);
        }

        (count($where) > 0) ? $whereStr = ' WHERE' : $whereStr = '';
        $counter = 0;
        foreach ($where as $key => $value) {
            if ($value[0] == 'C') {
                $value[1] = '"' . $value[1] . '"';
            }
            ($counter == 0) ? $and = '' : $and = 'AND ';
            $whereStr = $whereStr . ' ' . $and . $key . ' = ' . $value[1];
            $counter++;
        }

        $sql = 'SELECT ' . $fieldStr . ' FROM ' . $table . '' . $whereStr;
        $result = $this->connection->query($sql);
        $result = $result->fetchAll();
        return $result;
    }

    /**
     * Updates a entry in table.
     *
     * @param string $table
     * @param array $fields
     * @param array $where
     * @return bool|PDOStatement
     */
    public function update(string $table, $fields = [], $where = [])
    {
        $set = [];
        foreach ($fields as $key => $value) {
            ($value[0] == 'C') ? $valueStr = '"' . $value[1] . '"' : $valueStr = $value[1];
            $set[] = $key . ' = ' . $valueStr;
        }
        $set = implode(',', $set);

        (count($where) > 0) ? $whereStr = ' WHERE' : $whereStr = '';
        $whereArr = [];
        foreach ($where as $key => $value) {
            if ($value[0] == 'C') {
                $value[1] = '"' . $value[1] . '"';
            }
            $whereArr[] = $key . ' = ' . $value[1];
        }
        $where = trim($whereStr . ' ' . implode(' AND ', $whereArr));
        $sql = 'UPDATE ' . $table . ' SET ' . $set . ' ' . $where;
        return $this->connection->query($sql);
    }

    /**
     * Delte entry from table.
     *
     * @param string $table
     * @param array $where
     * @return bool|PDOStatement
     */
    public function delete(string $table, $where = [])
    {
        (count($where) > 0) ? $whereStr = ' WHERE' : $whereStr = '';
        $counter = 0;
        foreach ($where as $key => $value) {
            if ($value[0] == 'C') {
                $value[1] = '"' . $value[1] . '"';
            }
            ($counter == 0) ? $and = '' : $and = 'AND ';
            $whereStr = $whereStr . ' ' . $and . $key . ' = ' . $value[1];
            $counter++;
        }
        $sql = 'DELETE FROM ' . $table . $whereStr;

        return $this->connection->query($sql);
    }
}
