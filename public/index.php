<?php

declare(strict_types=1);

require '../bootstrap.php';

if (file_exists(TEMPPATH . '/lock/debug.lock')) {
    $display_errors = 'On';
} else {
    $display_errors = 'Off';
}
error_reporting(E_ALL);
ini_set('display_errors', $display_errors);
ini_set('session.name', $config->session->name);
ini_set('session.save_handler', 'files');
ini_set('session.save_path', SESSIONPATH);

// include helpers
$handle = opendir(HELPERPATH);
#var_dump($handle);die;
while($file = readdir($handle)) {
    if ($file != '.' && $file != '..' && substr($file, -10) == 'Helper.php') {
        include HELPERPATH . '/' . $file;
    }
}
closedir($handle);

$db = new DBHelper();

// include controllers

$handle = opendir(CONTROLLERPATH);
while($file = readdir($handle)) {
    if ($file != '.' && $file != '..' && substr($file, -14) == 'Controller.php') {
        include CONTROLLERPATH . '/' . $file;
    }
}
closedir($handle);

$router = new KRouter();
$router->dispatch();

$db = null;
