<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of System
 *
 * @author Aleksandar
 */

namespace Backend\Controllers;

use Backend\Abstraction\Controllers;

class System extends Controllers {

    public function getModules() {
        $modules = include 'backend/api/Definitions.php';
        foreach ($modules as $module) {
            $this->endMessage = json_encode($module);
        }
        
        $this->endStatus = 200;
    }

}
