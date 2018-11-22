<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Database {

    public static $instance;
    // url to db http://ambrosia.soliterinc.com/pma/
    private $_config = [
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'Wigashk6',
        'database' => 'game'
    ];
    public static $db = null;
    private $lastQuery = null;

    /* @return PDO */

    public function __construct() {
        $dsn = 'mysql:host=' . $this->_config['host'] . ';';
        $dsn .= 'dbname=' . $this->_config['database'];

        $options = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
        ];

        self::$db = new PDO($dsn, $this->_config['user'], $this->_config['password'], $options);
    }

    /**
     * 
     * @return \PDO
     */
    public static function getDatabase() {
        return self::$db;
    }

    public function buildUpdateQuery($table, array $identificators = [], array $properties = []) {
        if ($table && !empty($identificators) && !empty($properties)) {
            if (strpos($table, '`') !== 0) {
                $table = '`' . $table . '`';
            }

            $query = 'UPDATE ' . $table . ' SET ';

            $data = [];
            $mapper = function ($column, $value) use (&$data) {
                $data[] = '`' . $column . '` = :' . $column;
                return [':' . $column, $value];
            };

            $merged = array_merge($properties, $identificators);
            $mapped = array_map($mapper, array_keys($merged), $merged);

            $query .= implode(' , ', $data);
            $query .= ' WHERE';

            foreach ($identificators as $column => $value) {
                $query .= ' `' . $column . '` = :' . $column;
            }

            $queryBuilder = self::$db->prepare($query . ';');

            foreach ($mapped as $value) {
                $queryBuilder->bindValue($value[0], $value[1]);
            }
            $this->lastQuery = $queryBuilder;
            return $queryBuilder;
        }
    }

    public function buildInsertQuery($table, array $properties = []) {
        if ($table) {
            if (strpos($table, '`') !== 0) {
                $table = '`' . $table . '`';
            }

            $query = 'INSERT INTO ' . $table . ' SET ';

            $data = [];
            $mapper = function ($column, $value) use (&$data) {
                $data[] = '`' . $column . '` = :' . $column;
                return [':' . $column, $value];
            };

            $mapped = array_map($mapper, array_keys($properties), $properties);
            $query .= implode(' , ', $data);
            /* @var $queryBuilder \PDOStatement */
            $queryBuilder = self::$db->prepare($query . ';');

            foreach ($mapped as $value) {
                if (!$queryBuilder->bindValue($value[0], $value[1])) {
                    throw new Exception('Error binding ' + $value[0]);
                }
            }

            $this->lastQuery = $queryBuilder;
            return $queryBuilder;
        }
    }

    public function executeQuery($clear = true) {
        if ($this->lastQuery) {
            $execute = $this->lastQuery->execute();

            if ($clear) {
                $this->lastQuery = null;
            }

            return $execute;
        }
    }

    public function insertId() {
        return self::$db->lastInsertId();
    }

    public function buildSelectQuery($table, array $properties = [], $what = [], $limit = null) {
        if ($table) {
            if (empty($what)) {
                $implodeWhat = '*';
            } else {
                $implodeWhat = '`' . implode('`,`', $what) . '`';
            }

            if (strpos($table, '`') !== 0) {
                $table = '`' . $table . '`';
            }

            $query = 'SELECT ' . $implodeWhat . ' FROM ' . $table;
            $mapped = [];

            if (!empty($properties)) {
                $data = [];
                $mapper = function ($column, $value) use (&$data) {
                    $data[] = '`' . $column . '` = :' . $column;
                    return [':' . $column, $value];
                };

                $mapped = array_map($mapper, array_keys($properties), $properties);

                $query .= ' WHERE ' . implode(' AND ', $data);
            }

            if (is_numeric($limit)) {
                $query .= ' LIMIT ' . $limit;
            }

            $queryBuilder = self::$db->prepare($query . ';');

            foreach ($mapped as $value) {
                $queryBuilder->bindValue($value[0], $value[1]);
            }

            $this->lastQuery = $queryBuilder;
            return $queryBuilder;
        }

        return null;
    }

    public function fetchOne() {
        if ($this->lastQuery && $this->lastQuery->execute()) {
            return $this->lastQuery->fetch(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public function fetchAll() {
        if ($this->lastQuery && $this->lastQuery->execute()) {
            return $this->lastQuery->fetchAll(PDO::FETCH_ASSOC);
        }

        return [];
    }

    public static function instance() {
        if (!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}
