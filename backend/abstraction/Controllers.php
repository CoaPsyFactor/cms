<?php

namespace Backend\Abstraction;

abstract class Controllers {

    protected $post;
    protected $get;
    private $responseData = [
        'data' => [],
        'status' => 200
    ];

    public function setData($type = 'get', array $data = []) {
        $dataObj = (object) $data;

        switch ($type) {
            case 'post':
                $this->post = $dataObj;
                break;
            default :
                $this->get = $dataObj;
                break;
        }
    }

    public function __set($variable, $value) {
        if ($variable == 'endMessage') {
            $this->responseData['data'][] = $value;
        } else if ($variable == 'endStatus') {
            $this->responseData['status'] = (int) $value;
            $this->sendResponse();
        }
    }

    public function sendResponse() {
        if (isset($this->responseData['status'])) {
            $status = (int) $this->responseData['status'];
        } else {
            $status = 500;
        }

        http_response_code($status);

        return json_encode($this->responseData);
        die();
    }

}
