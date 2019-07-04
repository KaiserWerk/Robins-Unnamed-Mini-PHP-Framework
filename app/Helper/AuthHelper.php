<?php

class AuthHelper
{
    /**
     * Starts the session, independent from any authentication process
     *
     * @return null
     */
    public static function init()
    {
        global $config;
        if (!self::isSessionStarted()) {
            session_start();
        }
        
        #if (isset($_SESSION['activity']) && $_SESSION['activity'] + 600 < time()) {
        #    Helper::redirect('/auth/lockscreen');
        #}
        if (!isset($_SESSION['activity'])) {
            $_SESSION['activity'] = time();
        }
        
        // set the csrf token once
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = str_replace(".", "", uniqid('', true));
        }
        
        // Make sure we have a canary set
        if (!isset($_SESSION['canary'])) {
            if (self::isSessionStarted()) {
                session_regenerate_id();
                $_SESSION['sid'] = session_id();
                $_COOKIE[$config->session->name] = session_id();
                $_SESSION['canary'] = time();
            }
        }
        // Regenerate session ID every five minutes:
        if ($_SESSION['canary'] < time() - 300) {
            if (self::isSessionStarted()) {
                session_regenerate_id();
                $_SESSION['sid'] = session_id();
                $_COOKIE[$config->session->name] = session_id();
                $_SESSION['canary'] = time();
            }
        }
        
        return null;
    }
    
    /**
     * Checks whether the session is already started
     * session_status() is buggy, don't use it
     *
     * @return bool
     */
    private static function isSessionStarted()
    {
        #if (version_compare(phpversion(), '5.4.0', '>=')) {
        #    return (session_status() === PHP_SESSION_ACTIVE) ? true : false;
        #} else {
        return session_id() === '' ? false : true;
        #}
    }
    
    /**
     * Returns true if the current user is logged in
     *
     * @return bool
     */
    public static function isLoggedIn($debug = false)
    {
        global $config;
        
        if ($debug === true) {
            echo 'session[user]:'; echo isset($_SESSION['user']) ? 'yes' : 'no';
            echo '<br>session[sid]:'; echo isset($_SESSION['sid']) ? 'yes' : 'no';
            echo '<br>cookie(session):'; echo isset($_COOKIE[$config->session->name]) ? 'yes' : 'no';
            echo '<br>cookie(session) == session[sid]:'; echo ($_COOKIE[$config->session->name] == $_SESSION['sid']) ? 'yes' : 'no';
            die;
        }
        
        if (isset($_SESSION['user']) && isset($_SESSION['sid'])) {
            return isset($_COOKIE[$config->session->name]) && $_COOKIE[$config->session->name] == $_SESSION['sid'];
        }
        return false;
    }
    
    /**
     * Logs out the currently loggd in user
     *
     * @return bool
     */
    public static function logout()
    {
        global $config;
        @setcookie($config->session->name, '', time() - 10);
        @setcookie($config->cookie->login_attempt, '', time() - 10);
        @session_unset();
        @session_destroy();
        return true;
    }
}