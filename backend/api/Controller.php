<?php

/*
 * @author Aleksandar Zivanovic <coapsyfactor@gmail.com>
 */

namespace Backend\Api;

class Router {

    public $apiData = [];
    private $typeVar = null;
    private $type = null;
    private $module = null;
    private $method = null;
    private $errors = [];
    private $filters = [];
    protected $result = null;
    private $data = [
        INPUT_POST => [],
        INPUT_GET => []
    ];

    public function __construct() {
        $this->apiData = include 'backend/api/Definitions.php';

        if (filter_input(INPUT_GET, 'api')) {
            $this->filter();
        }
    }

    private function filter() {
        $this->data[INPUT_POST] = filter_input_array(INPUT_POST);

        if (!empty($this->data[INPUT_POST])) {
            $this->typeVar = 'post';
            $this->type = INPUT_POST;
        } else {
            $this->typeVar = 'get';
            $this->type = INPUT_GET;
        }

        $this->data[INPUT_GET] = filter_input_array(INPUT_GET);

        $this->module = (($module = $this->data[$this->type]['module']) ? $module : null);

        if (($method = $this->data[$this->type]['method'])) {
            $this->method = strtolower(str_replace('.', '', $method));
        } else {
            $this->method = null;
        }


        if (!$this->module) {
            $this->errors[] = 'Module is not valid';
        }

        if (!$this->method) {
            $this->errors[] = 'Method is not valid';
        }

        if (!array_key_exists($this->module, $this->apiData)) {
            $this->errors[] = 'Module "' . $this->module . '" doesn\'t exists';
        }

        if (!array_key_exists($this->method, $this->apiData[$this->module][$this->typeVar])) {
            $this->errors[] = 'Method "' . $this->method . '" is not valid';
        }

        if (empty($this->errors)) {
            $this->parse();
        }

        $this->finish();
    }

    private function parse() {
        $fields = $this->apiData[$this->module][$this->typeVar][$this->method];
        $dataFields = $this->data[$this->type];

        if (!isset($fields['ignore_token'])) {
            $fields['token'] = [
                'required' => true,
                'validators' => [
                    \Validators::VALIDATOR_VALID_TOKEN => []
                ],
                'filters' => [
                    \Filters::FILTER_FUNCTION_MODEL => [
                        \Filters::FILTER_MODEL_NAME => \Collections::USERS,
                        \Filters::FILTER_MODEL_SELECTOR => 'token'
                    ]
                ]
            ];
        } else {
            unset ($fields['ignore_token']);
        }

        foreach ($fields as $field => $fieldParams) {

            $sentField = null;
            $required = (isset($fieldParams['required']) && $fieldParams['required']);
            $validate = (isset($fieldParams['validators']) && is_array($fieldParams['validators']));
            $array = (isset($fieldParams['array']) && $fieldParams['array']);

            if (isset($dataFields[$field])) {
                $sentField = $dataFields[$field];
            } else if ($required) {
                $this->errors[] = "Field '{$field}' is required";
                continue;
            } else {
                continue;
            }

            if (isset($fieldParams['filters']) && is_array($fieldParams['filters'])) {
                $this->filters[$field] = $fieldParams['filters'];
            }

            if (!$validate) {
                continue;
            }

            if (!$array) {
                $this->validators($fieldParams['validators'], $sentField, $field);
                continue;
            }

            if ($array && !is_array($sentField)) {
                $this->errors[] = "Field '{$field}' ({$sentField}) isn't valid";
                continue;
            }

            foreach ($sentField as $v) {
                $this->validators($fieldParams['validators'], $v, $field);
            }
        }

        unset($this->data[$this->type]['method'], $this->data[$this->type]['module']);
    }

    private function filters($value, array $filters = []) {
        foreach ($filters as $filterFunction => $filterValue) {
            $filter = new \Filters($value);
            $value = $filter->$filterFunction($filterValue);
        }

        return $value;
    }

    private function validators(array $filters = [], $value = null, $field = '') {
        foreach ($filters as $validFunction => $data) {
            $validator = new \Validators($value);
            $validator->$validFunction($data);
            if (!$validator->validate()) {
                $this->errors[] = 'Field "' . $field . '" is not valid';
            }
        }
    }

    private function filterData($data) {

        $returnData = [];

        foreach ($data as $key => $value) {
            if (empty($this->filters[$key])) {
                $returnData[$key] = $value;
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $v) {
                    $rtv = $this->filters($v, $this->filters[$key]);
                    if (!is_null($rtv)) {
                        $returnData[$key][] = $rtv;
                    }
                }
            } else {
                $rtv = $this->filters($value, $this->filters[$key]);

                if (!is_null($rtv)) {
                    $returnData[$key] = $rtv;
                }
            }
        }
        return $returnData;
    }

    private function finish() {
        if (!empty($this->errors)) {
            http_response_code(500);
            $this->result = json_encode([
                'message' => $this->errors,
                'status' => 500
            ]);
        } else {
            $controller = ucfirst(strtolower($this->module));
            $functionStr = ucwords(str_replace('.', ' ', $this->method));
            $function = $this->typeVar . str_replace(' ', '', $functionStr);
            $neededClass = '\\Controllers\\' . $controller;

            if (class_exists($neededClass)) {
                $reflection = new ReflectionClass($neededClass);
                $class = $reflection->newInstance();

                $dataPost = (empty($this->data[INPUT_POST]) ? [] : $this->data[INPUT_POST]);
                $dataGet = (empty($this->data[INPUT_GET]) ? [] : $this->data[INPUT_GET]);

                $dataPostFilter = $this->filterData($dataPost);

                $class->setData('post', $dataPostFilter);
                $class->setData('get', $dataGet);

//                ob_start();
//
//                $results = ob_get_contents();
//                ob_clean();

                $result = $class->$function();
                $this->result = $result;
            } else {
                http_response_code(500);
                $this->result = json_encode([
                    'data' => ['There was some internal error, please contact Developer.'],
                    'status' => 500
                ]);
            }
        }

        die($this->result);
    }

}
