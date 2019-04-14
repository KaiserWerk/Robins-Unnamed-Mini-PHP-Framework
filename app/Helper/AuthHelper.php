<?php

class AuthHelper
{
    public static function init()
    {
        global $config;
        if (!self::isSessionStarted()) {
            session_start();
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
                $_COOKIE[$config['cookie']['sessid']] = session_id();
                $_SESSION['canary'] = time();
            }
        }
        // Regenerate session ID every five minutes:
        if ($_SESSION['canary'] < time() - 300) {
            if (self::isSessionStarted()) {
                session_regenerate_id();
                $_SESSION['sid'] = session_id();
                $_COOKIE[$config['cookie']['sessid']] = session_id();
                $_SESSION['canary'] = time();
            }
        }
        return null;
    }
    
    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            Helper::setMessage('You must be logged in.', 'error');
            Helper::redirect('/auth/login');
        }
    }
    
    public static function requireAdmin()
    {
        if (!self::isAdmin()) {
            Helper::setMessage('You are not authorized to do that.', 'error');
            Helper::redirect('/');
        }
    }
    
    public static function isAdmin($user_id = null)
    {
        if ($user_id != null) {
            $id = $user_id;
        } else {
            $id = (self::isLoggedIn()) ? $_SESSION['userid'] : null;
        }
        
        if ($id != null) {
            $db = new DBHelper();
            $row = $db->get('user', [
                'admin'
            ], [
                'id' => $id,
            ]);
            
            return (bool)$row['admin'];
        }
        
        return false;
    }
    
    private static function isSessionStarted()
    {
        return session_id() === '' ? false : true;
    }
    
    public static function isLoggedIn()
    {
        global $config;
        if (!self::isSessionStarted()) {
            session_start();
        }
        if (isset($_SESSION['userid']) && isset($_SESSION['sid'])) {
            if (isset($_COOKIE[$config['cookie']['sessid']]) && $_COOKIE[$config['cookie']['sessid']] == $_SESSION['sid']) {
                return true;
            }
            return false;
        }
        return false;
    }
    
    public static function hashPassword($string)
    {
        return password_hash($string, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    public static function generateToken($length = 60)
    {
        $chars = '01234567890123456789abcdefghijklmnopqrstuvwxyz';
        $token = '';
        for ($i = 0; $i < $length; ++ $i) {
            $len   = strlen($chars); // length of string
            $len   = $len - 1; // -1 because first array element is 0, not 1
            $int   = rand(0, $len); // generate random integer between 0 and string length -1
            $char  = $chars[$int]; // use random integer as array index to get a random character from string
            $token .= $char; // append it to the result string
        }
        return $token;
    }
    
    public static function getUserLocale()
    {
        global $config;
        if (self::isLoggedIn()) {
            $db  = new DBHelper();
            $row = $db->get('user', [
                'locale'
            ], [
                'id' => $_SESSION['userid']
            ]);
            // if the user exists
            if ($row != null) {
                return $row['locale']; // contains the user's locale
            }
        }
        
        if (isset($_COOKIE[$config['cookie']['language']])) {
            return $_COOKIE[$config['cookie']['language']];
        }
        
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $languages = ['de', 'en', 'fr', 'es'];
        if (in_array($lang, $languages)) {
            return $lang;
        }
        
        return $config['site']['default_locale'];
    }
}