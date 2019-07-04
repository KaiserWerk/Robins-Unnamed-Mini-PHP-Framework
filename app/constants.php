<?php
/**
 * Directories
 */
define('PROJECTPATH',     realpath(__DIR__ . '/..'));
define('CONTROLLERPATH',  PROJECTPATH . '/app/Controller');
define('LISTENERPATH',    PROJECTPATH . '/app/Listener');
define('HELPERPATH',      PROJECTPATH . '/app/Helper');
define('TEMPLATEPATH',    PROJECTPATH . '/app/Resources/templates');
define('TRANSLATIONPATH', PROJECTPATH . '/app/Resources/translations');
define('TEMPPATH',        PROJECTPATH . '/var');
define('LOCKPATH',        PROJECTPATH . '/var/lock');
define('LOGPATH',         PROJECTPATH . '/var/logs');
define('SESSIONPATH',     PROJECTPATH . '/var/sessions');
define('CACHEPATH',       PROJECTPATH . '/var/cache');

/**
 * Time-related
 */
define('DATEFORMAT',        'F d, Y H:i');
define('DATEFORMAT_DB',     'Y-m-d H:i:s');
define('MIN_IN_SECONDS',    60);
define('HOUR_IN_SECONDS',   MIN_IN_SECONDS * 60);
define('DAY_IN_SECONDS',    HOUR_IN_SECONDS * 24);
define('WEEK_IN_SECONDS',   DAY_IN_SECONDS * 7);

/**
 * File Icons
 */
define('FILEICON', [
    'doc' => 'fa fa-file-word-o',
    'docx' => 'fa fa-file-word-o',
    'txt' => 'fa fa-file',
    'pdf' => 'fa fa-file-pdf-o',
    'odt' => 'fa fa-file',
    'ppt' => 'fa fa-file-powerpoint-o',
    
    'zip' => 'fa fa-file-zip-o',
    'rar' => 'fa fa-file-zip-o',
    'gz' => 'fa fa-file-zip-o',
    'tar' => 'fa fa-file-zip-o',
    
    'mp3' => 'fa fa-music',
    'ogg' => 'fa fa-music',
    'wav' => 'fa fa-music',
    
    'mpg4' => 'fa fa-film',
    'mpg' => 'fa fa-film',
    'mpeg' => 'fa fa-film',
    'mp4' => 'fa fa-film',
    'avi' => 'fa fa-film',
    'mkv' => 'fa fa-film',
    'flv' => 'fa fa-film',
    
    'jpg' => 'fa fa-file-image-o',
    'jpeg' => 'fa fa-file-image-o',
    'gif' => 'fa fa-file-image-o',
    'png' => 'fa fa-file-image-o',
    
    'php' => 'fa fa-file-code-o',
    'js' => 'fa fa-file-code-o',
    'css' => 'fa fa-file-code-o',
    
    'xls' => 'fa fa-bar-chart-o',
    'ods' => 'fa fa-bar-chart-o',
    
    'n/a' => 'fa fa-file'
]);