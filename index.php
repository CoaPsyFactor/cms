<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'backend/AutoLoader.php';
new AutoLoader();
$router = new \Backend\Api\Router();

if (isset($_SESSION['logged'])) {
    $user = new \Models\User($_SESSION['logged']);
    echo 'Your are logged as: <strong>' . $user->username . '</strong><br />';   
}

echo '<script type="text/javascript" src="frontend/javascript/jquery.js"></script>';
echo '<script type="text/javascript" src="frontend/javascript/gameengine.js"></script>';

foreach ($router->apiData as $module => $method) {
    foreach ($method as $type => $data) {
        echo '<strong style="color: green;">' . strtoupper($type) . ' ' . $module . '</strong><br />';

        foreach ($data as $k => $v) {
            echo '<strong style="color: blue;">' . $k . '</strong><br /><p>';
            foreach ($v as $a => $vv) {
                $r = (isset($vv['required']) && $vv['required']);
                $color = ($r ? 'red' : 'lightblue');
                echo '<strong>' . $a . ' <span style="color: ' . $color . ';">' . ($r ? 'required' : 'not required') . '</span></strong><br />';
            }

            echo '</p>';
        }
    }
}
