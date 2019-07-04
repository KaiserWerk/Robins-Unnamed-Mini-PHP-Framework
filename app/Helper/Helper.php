<?php

use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;

class Helper
{
    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * @param $filename
     *
     * @return mixed
     */
    public static function getFilenameData($filename)
    {
        $parts = explode('.', $filename);
        $ending = $parts[count($parts)-1];
        unset($parts[count($parts)-1]);
        $name = implode('.', $parts);
        return [$name, $ending];
    }
    
    /**
     * @param $date
     * @param bool $db_format
     *
     * @return string
     */
    public static function getFormattedDate($date = null, $db_format = false)
    {
        $format = DATEFORMAT;
        if ($db_format === true) {
            $format = DATEFORMAT_DB;
        }
        return (new \DateTime($date))->format($format);
    }
    
    /**
     * @param $setting_name
     * @param null $default_value
     *
     * @return mixed|null
     */
    public static function getSetting($setting_name, $default_value = null)
    {
        global $db;
        $row = $db->get('setting', [
            'setting_value',
        ], [
            'setting_name' => $setting_name
        ]);
        
        if ($row === null || $row === false) {
            return $default_value;
        }
        
        return unserialize($row['setting_value']);
    }
    
    public static function setSetting($setting_name, $setting_value)
    {
        global $db;
        if ($db->get('setting', ['setting_value'], ['setting_name' => $setting_name])) {
            $db->update('setting', ['setting_value' => serialize($setting_value)], ['setting_name' => $setting_name]);
        } else {
            $db->insert('setting', [
                'setting_name' => $setting_name,
                'setting_value' => serialize($setting_value),
            ]);
        }
        
        return true;
    }
    
    /**
     * @param $message
     * @param string $type
     */
    public static function setMessage($message, $type = 'info')
    {
        $_SESSION['X-Message'] = $message;
        $_SESSION['X-Message-Type'] = $type;
    }
    
    public static function getMessage()
    {
        if (array_key_exists('X-Message', $_SESSION) && array_key_exists('X-Message-Type', $_SESSION)) {
            $message = $_SESSION['X-Message'];
            $type = $_SESSION['X-Message-Type'];
            unset($_SESSION['X-Message'], $_SESSION['X-Message-Type']);
            $partialFile = TEMPLATEPATH . '/partials/alert.tpl.php';
            if (!file_exists($partialFile)) {
                return $message;
            }
            $output = file_get_contents($partialFile);
            return sprintf($output, $type, $message);
        }
        return '';
    }
    
    /**
     * @param array $files
     * @param string $destination
     * @param bool $overwrite
     *
     * @return bool
     */
    public static function createZip($files = array(), $destination = '', $overwrite = false) {
        
        if (file_exists($destination) && !$overwrite) {
            return false;
        }

        $valid_files = array();
        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($file['file'])) {
                    $valid_files[] = array(
                        'file' => $file['file'],
                        'name' => $file['name'],
                    );
                }
            }
        }
        
        if (count($valid_files) > 0) {

            $zip = new ZipArchive();
            if ($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }

            foreach ($valid_files as $file) {
                $zip->addFile($file['file'], $file['name']);
            }
            $zip->close();

            return file_exists($destination);
        }
        
        return false;
    }
    
    public static function unzip($archive, $location)
    {
        $zip = new ZipArchive;
        $res = $zip->open($archive);
        if ($res === true) {
            $zip->extractTo($location);
            $zip->close();
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the current host name
     *
     * @return string
     */
    public static function getHost()
    {
        $possible_host_sources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
        $source_transformations = array(
            'HTTP_X_FORWARDED_HOST' => function($value) {
                $elements = explode(',', $value);
                return trim(end($elements));
            }
        );
        $host = '';
        foreach ($possible_host_sources as $source)
        {
            if (!empty($host)) break;
            if (empty($_SERVER[$source])) continue;
            $host = $_SERVER[$source];
            if (array_key_exists($source, $source_transformations))
            {
                $host = $source_transformations[$source]($host);
            }
        }
    
        // Remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);
    
        return 'http' . (self::isSSL() ? 's' : '') . '://' . trim($host);
    }

    /**
     * Return a slug for a given string. Optionally, can be used for
     * an URL safe filename
     *
     * @param $string
     * @param bool $is_filename
     * @return null|string
     */
    public static function sluggify($string, $is_filename = false, $lowercase = false) {
        $pattern = '/[^A-Za-z0-9]+/';
        if ($is_filename === true) {
            $pattern = '/[^A-Za-z0-9\.]+/';
        }
        
        $string = preg_replace($pattern, '-', $string);
        if ($lowercase === true) {
            $string = strtolower($string);
        }
        
        return $string;
    }

    /**
     * Determines whether the supplied username is already
     * in use or not
     *
     * @param $username
     * @return bool
     */
    public static function isUsernameInUse($username)
    {
        global $db;
        $bool = $db->has('user', [
            'username' => $username,
        ]);
        
        return $bool;
    }

    /**
     * Determines whether the supplied e-mail address is
     * already in use or not
     *
     * @param string $email
     * @return bool
     */
    public static function isEmailInUse($email)
    {
        global $db;
        $bool = $db->has('user', [
            'email' => $email,
        ]);
        
        return $bool;
    }

    /**
     * Generates an email tracking entry and returns a tracking token
     *
     * @param $confirmation_token
     * @param $recipient
     * @return string
     */
    public static function generateEmailTrackingToken($recipient, $confirmation_token = null)
    {
        global $db;
        $db->insert('mail_sent', [
            'confirmation_token' => $confirmation_token,
            'recipient' => $recipient,
            'sent_at' => date('Y-m-d H:i:s'),
        ]);
        $id = $db->id();
        
        return $id;
    }

    /**
     * Return the client's IP address in IPV4 format
     *
     * @return mixed
     */
    public static function getIP()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if(isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if(isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = false;
        }
        
        if ($ipaddress !== false) {
            return filter_var($ipaddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }
        
        return 'ip not found';
    }

    /**
     * Returns a transparent 1x1 pixel image used as a tracking pixel in e-mails
     *
     * @return resource
     */
    public static function generateTrackingPixel()
    {
        $img = imagecreatetruecolor(1, 1);
        imagesavealpha($img, true);
        $color = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $color);
        return $img;
    }

    /**
     * Redirects the user to the supplied url
     * (either internal or external)
     *
     * @param $url
     */
    public static function redirect($url)
    {
        header('Location: ' . $url);
        die;
    }

    /**
     * Returns the client's OS and Browser
     *
     * @return string
     */
    public static function getOSAndBrowser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = 'Unknown OS Platform';
        
        $os_array = array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
        
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform    =   $value;
            }
        }
        
        $browser  = 'Unknown Browser';
        $browser_array = array(
            '/msie/i'       =>  'Internet Explorer',
            '/firefox/i'    =>  'Firefox',
            '/safari/i'     =>  'Safari',
            '/chrome/i'     =>  'Chrome',
            '/edge/i'       =>  'Edge',
            '/opera/i'      =>  'Opera',
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/konqueror/i'  =>  'Konqueror',
            '/mobile/i'     =>  'Handheld Browser'
        );
        
        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser    =   $value;
            }
        }
        
        return $os_platform . '/' . $browser;
    }

    /**
     * Checks whether the connection was established over SSL
     * or not
     *
     * @return bool
     */
    public static function isSSL()
    {
        if (isset($_SERVER['HTTPS']) ) {
            if (strtolower($_SERVER['HTTPS']) === 'on')
                return true;
            if ($_SERVER['HTTPS'] == '1')
                return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @param $recipient
     * @param $subject
     * @param $body
     *
     * @return bool
     */
    public static function sendMail($recipient, $subject, $body)
    {
        if (Helper::getSetting('smtp_enabled') !== 'yes') {
            return mail($recipient, $subject, $body, 'From: '.Helper::getSetting('smtp_fullname').' <' . Helper::getSetting('smtp_username').'>');
        } else {
    
            $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
            try {
                //Server settings
                $mail->SMTPDebug = 0;                                 // Enable verbose debug output
                $mail->isSMTP();                                      // Set mailer to use SMTP
                $mail->Host       = Helper::getSetting('smtp_host');  // Specify main and backup SMTP servers
                $mail->SMTPAuth   = true;                               // Enable SMTP authentication
                $mail->Username   = Helper::getSetting('smtp_username');                 // SMTP username
                $mail->Password   = Helper::getSetting('smtp_password');                           // SMTP password
                $mail->SMTPSecure = Helper::getSetting('smtp_encryption');                            // Enable TLS encryption, `ssl` also accepted
                $mail->Port       = Helper::getSetting('smtp_port');                                    // TCP port to connect to
        
                //Recipients
                $mail->setFrom(Helper::getSetting('smtp_username'), Helper::getSetting('smtp_fullname'));
                #$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
                $mail->addAddress($recipient);               // Name is optional
                #$mail->addReplyTo('info@example.com', 'Information');
                #$mail->addCC('cc@example.com');
                #$mail->addBCC('bcc@example.com');
        
                //Attachments
                #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
                #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        
                //Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = $subject;
                $mail->Body    = $body;
                #$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
                $mail->send();
                #echo 'Message has been sent';
            } catch (PHPMailerException $e) {
                trigger_error('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        
                return false;
            }
    
            return true;
        }
    }
}
