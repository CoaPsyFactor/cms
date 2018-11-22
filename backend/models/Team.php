<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Models;

use Backend\Abstraction\Model;

/**
 * Description of Team
 *
 * @author Aleksandar
 */
class Team extends Model {

    public $leader;
    public $players = [];
    protected $table = \Tables::TABLE_TEAMS;

    protected function _build(array $data) {
        if (!empty($data['leader'])) {
            $this->leader = (int) $data['leader'];
        }

        if (!empty($data['players'])) {
            $this->players = json_decode($data['players'], true);
        } else {
            $this->players = [];
        }
    }

    protected function _extract(&$data) {
        if ($this->leader) {
            $data['leader'] = $this->leader;
        }

        if ($this->players) {
            $data['players'] = json_encode($this->players);
        }
    }

    public function valid() {
        return $this->uniqueId;
    }

//put your code here
}
