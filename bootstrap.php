<?php

// require the composer autloader
require_once __DIR__ . '/vendor/autoload.php';

// space for special commands / ini_set
ini_set('max_execution_time', '300');
ini_set('session.save_handler', 'files');
ini_set('session.save_path', SESSIONPATH);
ini_set('session.cookie_lifetime', SESSIONPATH);

// read the configuration file
$config = [];
try {
    $config = \Symfony\Component\Yaml\Yaml::parseFile(__DIR__ . '/config.yml');
} catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
    die('Could not parse config.yml: ' . $e->getMessage());
}

// include event listeners
$handle = opendir(LISTENERPATH);
while($file = readdir($handle)) {
    if ($file != '.' && $file != '..' && substr($file, -12) == 'Listener.php') {
        include LISTENERPATH . '/' . $file;
    }
}
closedir($handle);

// include controllers
$handle = opendir(CONTROLLERPATH);
while($file = readdir($handle)) {
    if ($file != '.' && $file != '..' && substr($file, -14) == 'Controller.php') {
        include CONTROLLERPATH . '/' . $file;
    }
}
closedir($handle);

// include helpers
$handle = opendir(HELPERPATH);
while($file = readdir($handle)) {
    if ($file != '.' && $file != '..' && substr($file, -10) == 'Helper.php') {
        include HELPERPATH . '/' . $file;
    }
}
closedir($handle);

// dispatch the routes
$router = new KRouter();
$router->dispatch();