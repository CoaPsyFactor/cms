<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Abstraction;

abstract class Model {

    public $id;
    public $uniqueId;
    public $created;
    public $updated;
    protected $table;
    protected $redis = false;

    public abstract function valid();

    protected abstract function _extract(&$data);

    protected abstract function _build(array $data);

    public function __construct(array $data = []) {
        $this->build($data);
    }

    public function build(array $data = []) {
        if (isset($data['id'])) {
            $this->id = (int) $data['id'];
            unset($data['id']);
        }

        if (isset($data['uniqueid'])) {
            $this->uniqueId = $data['uniqueid'];
            unset($data['uniqueid']);
        }

        if (isset($data['created'])) {
            $this->created = (int) $data['created'];
            unset($data['created']);
        }

        if (isset($data['updated'])) {
            $this->updated = (int) $data['updated'];
            unset($data['updated']);
        }

        $this->_build($data);
    }

    public function get(array $properties = []) {
        if (!$this->table) {
            throw new \Exception('Table not defined');
        }

        if (empty($properties)) {
            $properties = $this->extract();
        }

        $updateRedis = false;

        if ($this->redis) {
            $redis = \RedisClient::instance();

            if (!empty($properties['uniqueid']) && $redis->hExists($this->table, $properties['uniqueid'])) {
                $result = $redis->hGet($this->table, $properties['uniqueid']);
                $result = json_decode($result, true);

                $this->build($result);
                return;
            } else {
                $updateRedis = true;
            }
        }

        /* @var $db \Database */
        $db = \Database::instance();
        $db->buildSelectQuery($this->table, $properties);
        $result = (($fetch = $db->fetchOne()) ? $fetch : []);
        $this->build($result);

        if ($updateRedis) {
            $redis->hSet($this->table, $this->uniqueId, $this->toJson());
        }
    }

    public function save() {
        $time = time();
        $this->updated = $time;

        if ($this->table) {
            /* @var $db \Database */
            $db = \Database::instance();
            if ($this->id) {
                $db->buildUpdateQuery(
                        $this->table, ['id' => $this->id], $this->extract()
                );
            } else {
                $this->created = $time;
                $this->uniqueId = \Utils::uniqueId();
                $db->buildInsertQuery($this->table, $this->extract());
            }
            
            $db->executeQuery();

            return $this->updateId();
        } else {
            throw new Exception('Table not defined');
        }
    }

    public function toJson(array $fields = []) {
        if (empty($fields)) {
            $data = $this->extract();
        } else {
            foreach ($this->extract() as $key => $value) {
                if (!in_array($key, $fields)) {
                    continue;
                }

                $data[$key] = $value;
            }
        }

        return json_encode($data);
    }

    public function extract() {
        $data = [];
        $this->_extract($data);

        if ($this->id) {
            $data['id'] = $this->id;
        }

        if ($this->uniqueId) {
            $data['uniqueid'] = $this->uniqueId;
        }

        if ($this->updated) {
            $data['updated'] = $this->updated;
        }

        if ($this->created) {
            $data['created'] = $this->created;
        }

        return $data;
    }

    private function updateId() {
        $db = \Database::instance();
        if (!$this->id) {
            $this->get(['id' => $db->insertId()]);
        }

        if ($this->redis && $this->uniqueId) {
            $redis = \RedisClient::instance();
            $redis->hSet($this->table, $this->uniqueId, $this->toJson());
        }

        return $this;
    }

}
