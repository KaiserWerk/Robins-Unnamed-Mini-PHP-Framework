<?php
/**
 * Directories
 */
define('PROJECTPATH', __DIR__);
define('CONTROLLERPATH', __DIR__ . '/app/Controller');
define('LISTENERPATH', __DIR__ . '/app/Listener');
define('HELPERPATH', __DIR__ . '/app/Helper');
define('MODELPATH', __DIR__ . '/app/Model');
define('REPOSITORYPATH', __DIR__ . '/app/Repository');
define('TEMPLATEPATH', __DIR__ . '/app/Resources/templates');
define('TRANSLATIONPATH', __DIR__ . '/app/Resources/translations');
define('TEMPPATH', __DIR__ . '/var');
define('LOGPATH', __DIR__ . '/var/logs');
define('LOCKPATH', __DIR__ . '/var/lock');
define('SESSIONPATH', __DIR__ . '/var/sessions');
define('CACHEPATH', __DIR__ . '/var/cache');

/**
 * Time-related
 */
define('MIN_TO_SECONDS', 60);
define('HOUR_TO_SECONDS', MIN_TO_SECONDS * 60);
define('DAY_TO_SECONDS', HOUR_TO_SECONDS * 24);
define('WEEK_TO_SECONDS', DAY_TO_SECONDS * 7);