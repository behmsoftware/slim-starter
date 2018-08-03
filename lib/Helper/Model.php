<?php
/**
 * Created by PhpStorm.
 * User: behme
 * Date: 02.06.2018
 * Time: 16:21
 */

namespace Slim\Helper;
use Slim\Exception\ContainerValueNotFoundException;
use Slim\Helper\Database;
use Slim\Helper\Container;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Slim\Http\Request;
use Slim\Http\Response;
use PDO;


/**
 * Class Model
 */
abstract class Model
{
    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $db;

    /**
     * @var
     */
    private $prefix = DB_PRAEFIX;

    /**
     * @var
     */
    private $table;

    /**
     * @var
     */
    protected $container;

    /**
     * @var
     */
    private $connection;


    /**
     * Model constructor.
     * @throws ReflectionException
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $reflect = new ReflectionClass($this);
        if (is_null($this->table)){
            $table = array_reverse(explode('\\',$reflect->getName()));
            $output = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $table[0]));
            $this->table = strtolower($output);
        }
    }

    /**
     * Open DB connection.
     */
    private function openDBConnection()
    {
        $this->connection = new Database($this->db);
    }

    /**
     * Close DB Connection.
     */
    private function closeDBConnection()
    {
        $this->connection = null;
    }

    /**
     * Save entry.
     *
     * @throws ReflectionException
     */
    public function save()
    {
        $reflect = new ReflectionClass($this);
        $props   = $reflect->getProperties(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);
        $methods   = $reflect->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);
        $fields = [];
        foreach ($props as $prop) {
            if ($prop->getName() == 'id') {
                continue;
            }
            foreach ($methods as $method) {
                $method = $method->getName();
                $getter = 'get' . ucfirst($prop->getName());
                if ($method == $getter) {
                    $usedMethod = $reflect->getMethod($getter);
                    $value = $usedMethod->invoke($this);
                    $fields[$prop->getName()] = array('C', $value);
                }
            }
        }
        $table = $this->prefix . $this->table;

        $this->openDBConnection();
        if(empty($this->id)) {
            $this->setId($this->connection->create($table, $fields));
        } else {
            $this->connection->update($table, $fields, ['id' => ['N', $this->id]]);
        }
        $this->closeDBConnection();
    }

    /**
     * Delete entry by id.
     *
     * @param $id
     */
    public function delete($id) {
        $this->openDBConnection();
        $where = array(
            'id'     => array('N', $id),
        );
        $this->connection->delete(strtolower($this->prefix . $this->table), $where);
        $this->closeDBConnection();
    }

    /**
     * Find entry by id.
     *
     * @param int $id
     * @return mixed
     * @throws ReflectionException
     */
    public function findById(int $id) {
        $reflect = new ReflectionClass($this);
        $model = $reflect->newInstanceArgs([$this->db]);

        $where = array(
            'id'     => array('N', $id),
        );

        $this->openDBConnection();
        $result = $this->connection->read(strtolower($this->prefix . $this->table), [], $where);
        $this->closeDBConnection();

        $methods   = $reflect->getMethods(ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED);
        foreach ($result[0] as $key => $value) {
            foreach ($methods as $method) {
                $method = $method->getName();
                $setter = 'set' . ucfirst($key);
                if ($method == $setter) {
                    $usedMethod = $reflect->getMethod('set' . ucfirst($key));
                    $usedMethod->invoke($model, $value);
                }
            }

        }

        return $model;
    }

    /**
     * Find all entries.
     *
     * @return array
     * @throws ReflectionException
     */
    public function findAll()
    {
        $reflect = new ReflectionClass($this);
        $this->openDBConnection();
        $result = $this->connection->read(strtolower($this->prefix . $this->table), ['id']);
        $this->closeDBConnection();

        $resultObjects = [];
        foreach ($result as $key => $value) {
            $resultObjects[] = $this->findById($value['id']);
        }

        return $resultObjects;
    }

    /**
     * @return mixed
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @param array $params
     * @return array|\Slim\Helper\Container
     */
    public function find($params = array())
    {
        $this->openDBConnection();
        foreach ($params as $key => $value) {
            if ($value != "") {
                $where[$key] = array('C', $value);
            }
        }

        $result = $this->connection->read(strtolower($this->prefix . $this->table), [], $where);
        $container = array();
        $container = new Container();

        foreach ($result as $entry) {
            $contact = new $this($this->db);
            //$container[] = $contact->findById($entry['id']);
            $container->set($contact->findById($entry['id']));
        }
        $this->closeDBConnection();
        return $container;
    }

    /**
     * @param array $args
     * @return \Slim\Helper\Container
     */
    public function where($params = array())
    {
        $this->openDBConnection();
        foreach ($params as $key => $value) {
            if ($value != "") {
                $where[$key] = array('C', $value);
            }
        }

        $result = $this->connection->read(strtolower($this->prefix . $this->table), [], $where);
        $container = new Container();
        foreach ($result as $entry) {
            $contact = new $this($this->db);
            $container->set($contact->findById($entry['id']));
        }
        $this->closeDBConnection();
        return $container;
    }
}
