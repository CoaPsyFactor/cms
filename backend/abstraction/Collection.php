<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Abstraction;

use Backend\Abstraction\Model;

abstract class Collection extends \ArrayObject {

    private static $instance = [];
    protected $model = null;

    public function getAll(array $properties) {
        $data = [];

        foreach ($properties as $property) {
            $data[] = $this->getOne($property);
        }

        return $data;
    }

    public function getOne(array $properties) {
        $this->validModel();

        if (empty($properties)) {
            return null;
        }

        if (isset ($properties['id']) && !empty($this[$properties['id']])) {
            return $this[$properties['id']];
        }

        $modelClass = new ReflectionClass($this->model);

        $model = $modelClass->newInstance([]);
        $model->get($properties);

        if ($model->valid()) {
            $this[$model->id] = $model;
        }

        return $model;
    }

    private function validModel() {
        if (!($this->model instanceof Model)) {
            throw new Exception('Model entity must be instance of \\Abstracts\\Model', 1);            
        }
    }

    public static function instance($class = null) {
        if (!$class || !class_exists($class)) {
            return null;
        }

        if (!isset(self::$instance[$class]) || !self::$instance[$class]) {
            self::$instance[$class] = new $class;
        }

        return self::$instance[$class];
    }

    protected function fetch($table = \Tables::TABLE_USERS, $properties = [], $single = false) {
        /* @var $db \Database */
        $db = \Database::instance();
        $db->buildSelectQuery($table, $properties);
        $result = $db->fetchAll();

        if ($single && isset($result[0])) {
            return $result[0];
        } else if (!$single) {
            return $result;
        }

        return [];
    }

}
