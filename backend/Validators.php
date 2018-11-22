<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Validators {

    const VALIDATOR_NOT_VALID = null;
    const VALIDATOR_FUNCTION_EMAIL = 'email';
    const VALIDATOR_FUNCTION_LENGTH = 'length';
    const VALIDATOR_LENGTH_MIN = 'minlength';
    const VALIDATOR_LENGTH_MAX = 'maxlength';
    const VALIDATOR_FUNCTION_SAME = 'same';
    const VALIDATOR_COMPARE_VALUE = 'comparevalue';
    const VALIDATOR_ALLOW_EMPTY = 'allowempty';
    const VALIDATOR_FUNCTION_RANGE = 'range';
    const VALIDATOR_RANGE_MIN = 'minrange';
    const VALIDATOR_RANGE_MAX = 'maxrange';
    const VALIDATOR_FUNCTION_USERID = 'userid';
    const VALIDATOR_ID = 'id';
    const VALIDATOR_VALID_ENTITY = 'entity';
    const VALIDATOR_ENTITY = 'useentity';
    const VALIDATOR_ENTITY_SELECTOR = 'selector';
    const VALIDATOR_ENTITY_DEFAULT_SELECTOR = 'id';
    const VALIDATOR_VALID_TOKEN = 'validToken';

    protected $value;

    public function __construct($value = self::VALIDATOR_NOT_VALID) {
        $this->value = $value;
        return $this;
    }

    public function entity(array $data = []) {
        if (empty($data[self::VALIDATOR_ENTITY])) {
            $this->value = self::VALIDATOR_NOT_VALID;
            return;
        }

        $entity = $data[self::VALIDATOR_ENTITY];

        if (!class_exists($entity)) {
            throw new Exception("Entity {$entity} not defined");
        }

        $helper = \Abstracts\Collection::instance($entity);

        if (!empty($data[self::VALIDATOR_ENTITY_SELECTOR])) {
            $selector = $data[self::VALIDATOR_ENTITY_SELECTOR];
        } else {
            $selector = self::VALIDATOR_ENTITY_DEFAULT_SELECTOR;
        }

        $model = $helper->getOne([
            $selector => $this->value
        ]);

        if (!$model->valid()) {
            $this->value = self::VALIDATOR_NOT_VALID;
        }
    }

    public function email() {
        $this->value = filter_var($this->value, FILTER_VALIDATE_EMAIL);

        if (!$this->value) {
            $this->value = self::VALIDATOR_NOT_VALID;
        }

        return $this->value;
    }

    public function length(array $data) {
        $min = 0;
        $max = 1024;

        if (isset($data[self::VALIDATOR_LENGTH_MIN])) {
            $min = (int) $data[self::VALIDATOR_LENGTH_MIN];
        }

        if (isset($data[self::VALIDATOR_LENGTH_MAX])) {
            $max = (int) $data[self::VALIDATOR_LENGTH_MAX];
        }

        if (strlen($this->value) > $max || strlen($this->value) < $min) {
            $this->value = self::VALIDATOR_NOT_VALID;
        }

        return $this->value;
    }

    public function same(array $data) {
        $allowEmpty = (isset($data[self::VALIDATOR_ALLOW_EMPTY]) && $data[self::VALIDATOR_ALLOW_EMPTY]);
        $compareValue = (isset($data[self::VALIDATOR_COMPARE_VALUE]) ? $data[self::VALIDATOR_COMPARE_VALUE] : '');

        if ((!$allowEmpty && !strlen($compareValue)) || $this->value !== $compareValue) {
            $this->value = self::VALIDATOR_NOT_VALID;
        }

        return $this->value;
    }

    public function validToken() {
        $redis = \RedisClient::instance();

        if ($redis->hExists('tokens', $this->value)) {
            $this->value = $redis->hGet('tokens', $this->value);
            return;
        }

        $token = new \Models\Token([]);
        $token->get([
            'uniqueid' => $this->value
        ]);

        if ($token->valid()) {
            $this->value = $token->user;
        } else {
            $this->value = self::VALIDATOR_NOT_VALID;
        }
    }

    public function range(array $data) {
        if (isset($data[self::VALIDATOR_RANGE_MIN])) {
            $min = $data[self::VALIDATOR_RANGE_MIN];
        } else {
            $min = 0;
        }

        if (isset($data[self::VALIDATOR_RANGE_MAX])) {
            $max = $data[self::VALIDATOR_RANGE_MAX];
        } else {
            $max = 0;
        }

        $value = (int) $this->value;

        if ($value < $min || $value > $max) {
            $this->value = self::VALIDATOR_NOT_VALID;
        }

        return (int) $this->value;
    }

    public function userid(array $data = []) {
        $valid = false;

        if (isset($data[self::VALIDATOR_ID])) {
            $user = new \Models\User([]);
            $user->get(['id' => (int) $this->value]);

            $valid = $user->valid();
        } else {
            $u = new \Controllers\Users();
            $a = $u->logged();

            if (is_null($a) || !is_null($a) && $a->id != (int) $this->value) {
                $valid = false;
            } else {
                $valid = true;
            }
        }

        if (!$valid) {
            $this->value = self::VALIDATOR_NOT_VALID;
        }

        return (int) $this->value;
    }

    const VALIDATOR_FUNCTION_TEAM = 'inTeam';
    const VALIDATOR_TEAMID = 'id';

    public function inTeam(array $data = []) {
        if (isset($data[self::VALIDATOR_ID])) {

            /* @var $teamHelper \Collection\Teams */
            $teamHelper = \Abstracts\Collection::instance(\Collections::TEAMS);
            $teamHelper->getAll();
        }
    }

    public function validate() {
        return $this->value;
    }

}
