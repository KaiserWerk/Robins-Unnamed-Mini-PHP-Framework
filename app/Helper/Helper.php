<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\PHPMailerException;

class Helper
{
    public static function setMessage($message, $type = 'info')
    {
        /*
         * info, warn, error, success
         */
        $_SESSION['Message'] = $message;
        $_SESSION['MessageType'] = $type;
    }
    
    public static function getMessage()
    {
        if (isset($_SESSION['Message']) && isset($_SESSION['MessageType'])) {
            $message = $_SESSION['Message'];
            $type = $_SESSION['MessageType'];
            unset($_SESSION['Message'], $_SESSION['MessageType']);
            $file = TEMPLATEPATH . '/other/message.tpl.html';
            if (!file_exists($file)) {
                $content = $message;
            } else {
                $content = file_get_contents($file);
                $content = str_replace('{message}', $message, $content);
                $content = str_replace('{type}', $type, $content);
            }
            return $content;
        }
        return '';
    }
    
    public static function redirect($target)
    {
        header('Location: ' . $target);
        die;
    }
    
    public static function getHost()
    {
        return 'http' . (self::isSSL() ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];
    }
    
    private static function isSSL() {
        if (isset($_SERVER['HTTPS']) ) {
            if ('on' == strtolower($_SERVER['HTTPS'])) {
                return true;
            }
            if ('1' == $_SERVER['HTTPS']) {
                return true;
            }
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }
    
    public static function sendMail($recipient, $subject, $body)
    {
        global $config;
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $config['mailer']['host'];  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $config['mailer']['address'];                 // SMTP username
            $mail->Password = $config['mailer']['password'];                           // SMTP password
            $mail->SMTPSecure = $config['mailer']['encryption'];                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = $config['mailer']['port'];                                    // TCP port to connect to
        
            //Recipients
            $mail->setFrom($config['mailer']['address'], $config['site']['title'] . ' Mailer');
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
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }
    }
}