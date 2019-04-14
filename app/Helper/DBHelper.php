<?php

use Medoo\Medoo;

class DBHelper extends Medoo {
    public function __construct() {
        global $config;
        parent::__construct( [
            'database_type' => $config['database']['driver'],
            'server'        => $config['database']['host'],
            'database_name' => $config['database']['dbname'],
            'username'      => $config['database']['username'],
            'password'      => $config['database']['password'],
            'prefix'        => $config['database']['prefix'],
            'port'          => $config['database']['port'],
            'charset'       => $config['database']['prefix'],
        ] );
        
    }
    
    public function hasError() {
        return ! empty( $this->error() ) && $this->error()[0] !== '00000';
    }
}
