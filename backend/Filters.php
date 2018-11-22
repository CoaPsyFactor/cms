<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Filters
 *
 * @author Aleksandar
 */
class Filters {

    const FILTER_FUNCTION_INT = 'integer';
    const FILTER_UNIQUEID = 'uniqueid';
    const FILTER_FUNCTION_MODEL = 'model';
    const FILTER_MODEL_NAME = 'modelname';
    const FILTER_MODEL_SELECTOR = 'modelselector';
    const FILTER_MODEL_DEFAULT_SELECTOR = 'id';

    private $value;

    public function __construct($value) {
        $this->value = $value;
        return $this;
    }

    public function parsetoken() {
        $tokenParse = new \Validators($this->value);
        $tokenParse->validToken();
        $id = $tokenParse->validate();

        if ($id != \Validators::VALIDATOR_NOT_VALID) {
            $userHelper = \Abstracts\Collection::instance(\Collection::USERS);
            $user = $userHelper->getOne([
                'id' => $id
            ]);

            return $user;
        }

        return new \Models\User([]);
    }

    public function integer() {
        $this->value = (int) $this->value;

        return $this->value;
    }

    public function model(array $data = []) {

        if (!isset($data[self::FILTER_MODEL_NAME])) {
            return null;
        }

        if (!class_exists($data[self::FILTER_MODEL_NAME])) {
            return null;
        }

        if (!empty($data[self::FILTER_MODEL_SELECTOR])) {
            $selector = $data[self::FILTER_MODEL_SELECTOR];
        } else {
            $selector = self::FILTER_MODEL_DEFAULT_SELECTOR;
        }

        $helper = \Abstracts\Collection::instance($data[self::FILTER_MODEL_NAME]);
        $model = $helper->getOne([
            $selector => $this->value
        ]);

        if (!is_null($model) && $model->valid()) {
            return $model;
        }

        return null;
    }

}
