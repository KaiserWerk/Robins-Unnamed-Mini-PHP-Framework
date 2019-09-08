<?php

class DBHelper extends \Medoo\Medoo
{
    public function __construct()
    {
        global $config;
        if ($config->database->in_use === true) {
            parent::__construct([
                'database_type' => $config->database->driver,
                'server'        => $config->database->host,
                'database_name' => $config->database->name,
                'username'      => $config->database->username,
                'password'      => $config->database->password,
                'prefix'        => $config->database->table_prefix,
                'port'          => $config->database->port,
                'charset'       => $config->database->charset,
                'database_file' => $config->database->file
            ]);
        }
    }
}
