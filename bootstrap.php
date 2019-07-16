<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/constants.php';

/**
 * Get the site configuration
 */
$config = [];
try {
    $yaml_file = __DIR__ . '/config.yml';
    if (!file_exists($yaml_file)) {
        die("Missing config file! Copy config.dist.yml to config.yml to get started!");
    }
    $yaml_content = file_get_contents($yaml_file);
    
    // replace placeholders in config file with actual values
    $const = get_defined_constants(true)['user'];
    foreach ($const as $key => $value) {
        if (!is_array($const)) {
            $yaml_content = str_replace('%' . $key . '%', $value, $yaml_content);
        }
    }
    
    // parse the file content
    $config = \Symfony\Component\Yaml\Yaml::parse($yaml_content);
    $config = json_decode(json_encode($config));
} catch (\Symfony\Component\Yaml\Exception\ParseException $e) {
    die('Could not parse config.yml: ' . $e->getMessage());
}
