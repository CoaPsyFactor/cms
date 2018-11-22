<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Collections;

use Backend\Models\Game;
use Backend\Models\User;
use Backend\Models\Collection;
use Backend\Abstraction\Collection as AbstractionCollection;
/**
 * Description of Games
 *
 * @author Aleksandar
 */
class Games extends AbstractionCollection {
    protected $model = \Models::GAME;

    public function getUserScheduledGames(User $player) {
        if (!$player->valid()) {
            return null;
        }

        $db = \Database::getDatabase();
        $q = "
            SELECT `games`.* FROM `teams`
            RIGHT JOIN
                `games`
            ON  
                (`games`.`teams` LIKE CONCAT('[', `teams`.`id`, ',%')
                OR `games`.`teams` LIKE CONCAT('%,', `teams`.`id`, ']'))
                AND `games`.`started` = 0
                AND `games`.`scheduled` >= :time
            WHERE
                `teams`.`leader` = :playerid
                OR `teams`.`players` LIKE CONCAT('[', :playerid,',%')
                OR `teams`.`players` LIKE CONCAT('%,', :playerid,']')
            ;";

        $time = time();

        $query = $db->prepare($q);
        $query->bindParam(':playerid', $player->id);
        $query->bindParam(':time', $time);

        return $this->getData($query);
    }

    private function getData($query) {
        if (!$query->execute()) {
            return null;
        }

        $return = [];

        foreach ($query->fetchAll() as $data) {
            $game = new Game($data);
            $this[$game->id] = $game;

            $return[$game->id] = $this[$game->id];
        }

        return $return;
    }

    public function getGameTeams(Game $game, $json = false) {
        $g = $this->getOne($game->extract());

        if (!$g->valid()) {
            return [];
        }

        if (!is_array ($g->teams)) {
            $g->team = [];
        }
        
        if ($json) {
            return $g->teams;
        } else if (($teams = json_decode($g->teams, true)) !== NULL) {
            return $teams;
        } else {
            return [];
        }
        
    }

    public function getUserActiveGames(\User $player) {
        if (!$player->valid()) {
            return null;
        }

        $db = \Database::getDatabase();
        $q = "
            SELECT `games`.* FROM `teams`
            RIGHT JOIN
                `games`
            ON  
                (`games`.`teams` LIKE CONCAT('[', `teams`.`id`, ',%')
                OR `games`.`teams` LIKE CONCAT('%,', `teams`.`id`, ']'))
                AND `games`.`started` != 0
            WHERE
                AND `teams`.`leader` = :playerid
                OR `teams`.`players` LIKE CONCAT('[', :playerid,',%')
                OR `teams`.`players` LIKE CONCAT('%,', :playerid,']')
            ;";

        $query = $db->prepare($q);
        $query->bindParam(':playerid', $player->id);

        return $this->getData($query);
    }
}
