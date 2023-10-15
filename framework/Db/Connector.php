<?php

namespace Majframe\Db;

use Majframe\Core\Core;

class Connector
{
    private static Connector|null $instance = null;
    protected string $host;
    protected string $port;
    protected string $user;
    protected string $password;
    protected string $database;

    protected \PDO $conn;

    private function __construct($env)
    {
        $this->host = $env['DB_HOST'];
        $this->port = $env['DB_USER'];
        $this->password = $env['DB_PASSWORD'];
        $this->user = $env['DB_USER'];
        $this->database = $env['DB_NAME'];

        //Make connection
        $this->conn = new \PDO('mysql:host=' . $this->host . ';dbname=' . $this->database, $this->user, $this->password);
    }

    public static function getConnector()
    {
        if(self::$instance == null) {
            self::$instance = new Connector(Core::getExistingInstance()->getEnv());
        }

        return self::$instance;
    }

    public function executeI(String $table, Array $fields) : bool|int
    {
        $sql = 'INSERT INTO ' . $table . ' ';
        $field_names = '(';
        $fields_value = '(';

        foreach ($fields as $key => $field) {
            if ($key != array_key_last($fields)) {
                $field_names .= $key . ', ';
                $fields_value .= ':' . $key . ', ';
            } else {
                $field_names .= $key . ')';
                $fields_value .= ':' . $key . ')';
            }
        }

        $sql .= $field_names . ' VALUE ' . $fields_value;

        $return = $this->conn->prepare($sql)->execute($fields);

        if ($return) {
            return $this->conn->lastInsertId();
        }

        return false;
    }
    public function executeU(String $table, Array $fields, Array $wheres = null) : bool
    {
        $sql = 'UPDATE ' . $table. ' SET';

        foreach ($fields as $key => $field) {
            $sql .= ' ' . $key . '=:' . $key;

            if ($key != array_key_last($fields)) {
                $sql .= ', ';
            } else {
                $sql .= ' ';
            }
        }

        if ($wheres != null) {
            $sql .= $this->buildWhere($wheres);
        }

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute($fields);
    }

    /**
     * TODO implement GROUP BY
     * @param String $table
     * @param array|null $wheres
     * @param array|null $orderBy
     * @param int|array|null $limit
     * @return Array
     * @throws \Exception
     */
    public function executeS(String $table, Array|null $wheres = null, Array|null $orderBy = null, Array|int|null $limit = null) : Array
    {
        $query = 'SELECT * FROM ' . $table;

        if ($wheres != null) {
            $query .= $this->buildWhere($wheres);
        }

        if ($orderBy != null) {
            if (!array_key_exists('order', $orderBy)) {
                $orderBy['order'] = '';
            }

            $query .= ' ORDER BY ' . $orderBy['field'] . ' ' . $orderBy['order'];
        }

        if ($limit != null) {
            if (is_array($limit)) {
                $query .= ' LIMIT ' . $limit['offset'] . ', ' . $limit['row_num'];
            } else {
                $query .= ' LIMIT ' . $limit;
            }
        }

        return $this->conn->query($query)->fetchAll();
    }

    public function executeCount(String $table, Array|null $wheres = null) : int
    {
        $query = 'SELECT COUNT(*) FROM ' . $table;

        if ($wheres != null) {
            $query .= $this->buildWhere($wheres);
        }

        return $this->conn->query($query)->fetch(\PDO::FETCH_ASSOC)['COUNT(*)'];
    }

    public function executeD(String $table, Array $wheres = null) : void {
        $query = 'DELETE FROM ' . $table;

        if ($wheres != null) {
            $query .= $this->buildWhere($wheres);
        }

        $this->conn->query($query)->fetch();
    }

    public function runS($query) : array {
        return $this->conn->query($query)->fetchAll();
    }
    public function getPDO() : \PDO
    {
        return $this->conn;
    }

    /**
     * TODO implement LIKE, IN, BETWEEN and also later IMPLEMENT subqueries for where
     *
     * Dev comment:
     * $wheres = [
     *      [
     *          'field'    => 'username',
     *          'operator' => '=',
     *          'value'    => 'MajFrame'
     *      ],
     *      [
     *          'field'    => 'email'
     *          'operator' => '='
     *          'value'    => 'majframe@majframe.com'
     *          'prev_rel' => 'AND'
     *      ]
     * ]
     */
    private function buildWhere(Array $wheres) : String
    {
        $query = '';
        foreach ($wheres as $key => $where) {
            if ($key != 0 && !array_key_exists('prev_rel', $where)) {
                throw new \Exception('The prev_rel (previous relationship) operator missing!');
            }

            if (!array_key_exists('operator', $where)) {
                $where['operator'] = '=';
            }

            if ($key == 0) {
                $where['prev_rel'] = 'WHERE';
            }

            if (is_string($where['value'])) {
                $where['value'] = '\'' . $where['value'] . '\'';
            }

            $query .= ' ' . $where['prev_rel'] . ' ' . $where['field'] . $where['operator'] . $where['value'];
        }

        return $query;
    }
}