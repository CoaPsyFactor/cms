<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Models;

use Backend\Abstraction\Model;
/**
 * Description of Game
 *
 * @author Aleksandar
 */
class Game extends Model {

    public $teams;
    public $started;
    public $scheduled;
    public $replay_steps;
    protected $table = \Tables::TABLE_GAMES;
    protected $redis = true;

    protected function _build(array $data) {
        if (!empty($data['teams'])) {
            if (is_array($data['teams'])) {
                $data['teams'] = json_encode($data['teams']);
            }
            
            $this->teams = $data['teams'];
        }

        if (isset($data['started'])) {
            $this->started = (bool) $data['started'];
        }

        if (isset($data['scheduled'])) {
            $this->scheduled = (int) $data['scheduled'];
        }

        if (isset($data['replay_steps'])) {
            $this->replay_steps = json_decode($data['replay_steps'], true);
        }
    }

    protected function _extract(&$data) {
        if ($this->teams) {
            $data['teams'] = json_encode($this->teams);
        }

        if ($this->started) {
            $data['started'] = $this->started;
        }

        if ($this->scheduled) {
            $data['scheduled'] = $this->scheduled;
        }

        if ($this->replay_steps) {
            $data['replay_steps'] = json_encode($this->replay_steps);
        }
    }

    public function valid() {
        return $this->uniqueId;
    }

//put your code here
}
