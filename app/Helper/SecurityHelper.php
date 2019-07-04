<?php

class SecurityHelper
{
    public static function checkCaptcha()
    {
        if (Helper::getSetting('auth_captcha_disabled') === 'yes') {
            return true;
        }
        if (!isset($_POST['_captcha'])) {
            return false;
        }
        
        return $_POST['_captcha'] == $_SESSION['captcha'];
    }
    
    public static function generateCaptchaInput()
    {
        if (Helper::getSetting('auth_captcha_disabled') === 'yes') {
            return '';
        }
        
        return '<div class="form-group">
        <img id="captcha_image" class="captcha-image" alt="Captcha" src="/security/captcha" style="display: inline-block;">
        <a href="javascript:refreshCaptcha();" title="Show another Captcha" style="margin-bottom: 8px;"><i class="fa fa-refresh"></i> </a><br>
        <input type="text" name="_captcha" class="form-control" placeholder="Captcha" required>
        </div>';
    }
    
    /**
     * Generates a random alphanumeric token, usually a confirmation token.
     * The foreign check for existing tokens can be ignored if you want to
     * generate a token for a different purpose.
     *
     * @param int $length
     * @param bool $ignore_foreign_check
     * @return string
     */
    public static function generateToken($length = 25, $ignore_foreign_check = false)
    {
        $chars = '01234567890123456789abcdefghijklmnopqrstuvwxyz';
        if ($length > 20) {
            $chars.= 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789';
        }
        $res = '';
        for ($i = 0; $i < $length; ++$i) {
            $res .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        //If confirmation token exists, generate another one
        if ($ignore_foreign_check !== true) {
            $checks = [];
            global $db;
            
            $checks[] = $db->has('user_action', [
                'token' => $res,
            ]);
            $checks[] = $db->has('game', [
                'token' => $res,
            ]);
            
            if ((bool)array_product($checks) !== false) {
                return self::generateToken($length);
            }
        }
        return $res;
    }
    
    /**
     * @param $string
     *
     * @return string
     */
    public static function hashPassword($string)
    {
        return password_hash($string, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Compares two strings (hashed) in a way timed attacks don't work
     *
     * @param $string1
     * @param $string2
     * @param int $pad_length
     *
     * @return bool
     */
    public static function str_cmp_sec($string1, $string2, $pad_length = 75)
    {
        $str1_parts = str_split(str_pad((string)$string1, $pad_length, '0', STR_PAD_RIGHT));
        $str2_parts = str_split(str_pad((string)$string2, $pad_length, '0', STR_PAD_RIGHT));
        $result_array = array();
        $i = 0;
        foreach ($str1_parts as $part) {
            if ($part === $str2_parts[$i]) {
                $result_array[$i] = true;
            } else {
                $result_array[$i] = false;
            }
            ++$i;
        }
        return (bool)array_product($result_array);
    }
    
    /**
     * Generates a hidden input field with the session's CSRF token.
     * Should be used in every form.
     *
     * @param bool $return
     * @return string
     */
    public static function generateCSRFInput()
    {
        return '<input type="hidden" name="_csrf_token" value="' . $_SESSION['_csrf_token'] . '">' . PHP_EOL;
    }
    
    /**
     * Checks whether the CSRF token from a form is (existing and) valid or not
     *
     * @return bool
     */
    public static function checkCSRFToken()
    {
        if ($_POST['_csrf_token'] !== null) {
            return $_POST['_csrf_token'] === $_SESSION['_csrf_token'];
        }
        return false;
    }
    
    /**
     * Generates a hidden input field which should not be filled in.
     * Should be used in every form.
     *
     * @param bool $return
     * @return string
     */
    public static function generateHoneypotInput()
    {
        return '<div style="display:none"><input type="text" name="_user_email" class="form-control"></div>'.PHP_EOL;
    }
    
    /**
     * Checks the form's honepot for input
     *
     * @return bool
     */
    public static function checkHoneypot()
    {
        if (empty($_POST['_user_email'])) {
            return true; // true = ok
        }
        return false;
    }
    
    /**
     * Insert at start of a route to require a logged in user
     */
    public static function requireLogin()
    {
        AuthHelper::init();
        if (!AuthHelper::isLoggedIn()) {
            Helper::setMessage('Please login first!', 'warning');
            Helper::redirect('/auth/login');
        }
    }
    
    public static function requireAdmin()
    {
        AuthHelper::init();
        self::requireLogin();
        global $db;
        $user = $db->get('user', [
            'access_level'
        ], [
            'id' => $_SESSION['user'],
        ]);
        
        if ($user['access_level'] < 3) {
            Helper::setMessage('You must have administrative rights to access this page.', 'error');
            Helper::redirect('/');
        }
    }
}