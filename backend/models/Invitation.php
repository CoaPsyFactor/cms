<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Models;

use Backend\Abstraction\Model;

/**
 * Description of Invitation
 *
 * @author Aleksandar
 */
class Invitation extends Model {

    public $user;
    public $invited_by;
    public $game;
    public $team;
    protected $table = \Tables::TABLE_INVITES;

    protected function _build(array $data) {
        if (isset($data['user'])) {
            $this->user = (int) $data['user'];
        }

        if (isset($data['invited_by'])) {
            $this->invited_by = (int) $data['invited_by'];
        }

        if (isset($data['game'])) {
            $this->game = (int) $data['game'];
        }

        if (isset($data['team'])) {
            $this->team = (int) $data['team'];
        }
    }

    protected function _extract(&$data) {
        if ($this->user) {
            $data['user'] = $this->user;
        }

        if ($this->invited_by) {
            $data['invited_by'] = $this->invited_by;
        }

        if ($this->game) {
            $data['game'] = $this->game;
        }

        if ($this->team) {
            $data['team'] = $this->team;
        }
    }

    public function valid() {
        return $this->uniqueId;
    }

//put your code here
}
