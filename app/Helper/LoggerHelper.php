<?php

class LoggerHelper
{
    /**
     * Writes a debug log file
     *
     * External writing means sending data to a logging API
     *
     * @param string $content
     * @param string $log_level
     * @return bool
     */
    public static function debug($content, $log_level = 'debug')
    {
        $log_levels = array(
            'debug',
            'info',
            'warn',
            'error',
            'crit',
        );
        
        if (!is_dir(LOGPATH)) {
            @mkdir(LOGPATH, 0775);
        }

        $file_single = LOGPATH . '/debug.log';

        $h = @fopen($file_single, 'ab+');
        if ($h !== false) {
            if (!in_array($log_level, $log_levels, true)) {
                $log_level = 'debug';
            }
            $log_line_raw = str_pad(strtoupper($log_level), 5, ' ', STR_PAD_LEFT);
            $log_line = '['.date('Y-m-d H:i:s').'] [' . Helper::getIP() . '] [' . $log_line_raw . '] ' . $content . PHP_EOL;
            @fwrite($h, $log_line);
            @fclose($h);
        } else {
            return false;
        }
        
        return true;
    }
    
    /**
     * Logs a login attempt (be it successful or not)
     *
     * @param $user_entry_id
     * @param $login_status
     * @return bool
     */
    public static function loginAttempt($user_entry_id, $login_status)
    {
        global $db;
        $db->insert('login', [
            'user_entry_id' => $user_entry_id,
            'login_status' => $login_status,
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'lang' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
            'ip' => ip2long(Helper::getIP()),
        ]);
        
        return true;
    }
}