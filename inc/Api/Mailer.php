<?php


namespace Slash\Api;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

final class Mailer
{
    protected static $mail = null;

    protected function __construct()
    {
        // Empty on purpose
    }

    public static function instance()
    {
        if (self::$mail === null) {
            self::$mail = self::init();
        }
        return self::$mail;
    }

    protected static function init()
    {
        $smtp_options = self::get_smtp_credentials();

        $mail = new PHPMailer(true);
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host = 'smtp1.example.com';                    // Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = 'user@example.com';                     // SMTP username
        $mail->Password = 'secret';                               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        return $mail;
    }

    protected static function get_smtp_credentials()
    {
        //TODO:
        return [];
    }

    public function sendEmail()
    {

    }

    protected function __clone()
    {
        // Empty on purpose
    }
}