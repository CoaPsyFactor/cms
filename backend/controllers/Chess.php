<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Backend\Controllers;

use Backend\Abstraction\Controllers;

/**
 * Description of Chess
 *
 * @author Aleksandar
 */
class Chess extends Controllers {
    public function postDelegate() {
        $leader = $this->post->token;
        $delegator = $this->post->delegator;
        
    }
    
    public function postStart() {
        /* @var $game \Models\Game */
        $game = $this->post->game;
        
        
    }
}
