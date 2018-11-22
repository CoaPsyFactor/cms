<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Controllers;

use Backend\Abstraction\Controllers;
use Backend\Abstraction\Collection;

/**
 * Description of Game
 *
 * @author Aleksandar
 */
class Game extends Controllers {

    public function postHost() {
        /* @var $gameHelper \Collection\Games */
        $gameHelper = Collection::instance(\Collections::GAMES);
        $started = $gameHelper->getUserActiveGames($this->post->token);
        $err = false;

        if (!empty($started)) {
            $this->endMessage = 'You have already started game';
            $err = true;
        }

        $scheduledGames = $gameHelper->getUserScheduledGames($this->post->token);
        $scheduled = false;

        foreach ($scheduledGames as $game) {
            if ($game->scheduled - 600 <= time()) {
                $scheduled = true;
                break;
            }
        }

        if ($scheduled) {
            $this->endMessage = 'You have scheduled game in less than 10 minutes';
            $err = true;
        }

        if ($err) {
            $this->endStatus = 401;
        }

        $time = time();
        $uniqueid = uniqid(round(microtime(true)), true);

        /* @var $game \Models\Game */
        $game = $gameHelper->addOne([
            'leader' => $this->post->token->id,
            'teams' => '',
            'created' => $time,
            'updated' => $time,
            'uniqueid' => $uniqueid,
            'started' => 0,
            'scheduled' => $this->post->scheduled
        ]);
        
        if ($game->valid()) {
            $this->endMessage = $game->extract();
            $this->endStatus = 200;
        }
        
        $this->endMessage = 'There were some errors while creating the game.';
        $this->endStatus = 500;
    }
}
