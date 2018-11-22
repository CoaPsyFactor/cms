<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Models;

use Backend\Abstraction\Model;

/**
 * Description of Players
 *
 * @author Aleksandar
 */
class Player extends Model {
    protected $table = \Tables::TABLE_PLAYERS;
    public $user;
    public $permission;
    
    protected function _build(array $data) {
        if (isset ($data['user'])) {
            $this->user = (int) $data['user'];
        }

        if (isset ($data['permission'])) {
            $this->permission = (int) $data['permission'];
        }
    }

    protected function _extract(&$data) {
        if ($this->user) {
            $data['user'] = $this->user;
        }

        if ($this->permission) {
            $data['permission'] = $this->permission;
        }
    }

    public function valid() {
        return $this->uniqueId;
    }
}