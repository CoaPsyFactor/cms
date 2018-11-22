<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class AutoLoader {

    public function __construct() {
        $loaddirs = [
            'backend',
            'backend/abstraction',
            'backend/models',
            'backend/collections',
            'backend/controllers',
            'backend/api'
        ];

        foreach ($loaddirs as $dir) {
            $files = scandir($dir);

            foreach ($files as $file) {
                $a = (($b = strpos($file, 'php') !== false) ? $b : -1);
                $ext = substr($file, -4, ($a + 3));
                if ($file != 'AutoLoader.php' && $file != '.' && $file != '..' && $ext == '.php') {
                    $readFile = $dir . '/' . $file;
                    include $readFile;
                }
            }
        }
    }

}
