<?php


namespace Slash\Base;

use PHPMailer;


class Mailer
{
    public function register()
    {
        // Hook phpmailer action
        add_action('phpmailer_init', [$this, 'init']);
    }

    public function init(PHPMailer $phpmailer)
    {
        // Get saved options
        $plugin_options = get_option('coupons_plugin');
        if (!$plugin_options) return;
        $smtp_options = $plugin_options['smtp'] ?? false;
        $mail_options = $plugin_options['mail'] ?? false;
        if (!$smtp_options || !$mail_options) return;

        $hook_settings = $smtp_options['settings'] ?? false;
        if ($hook_settings === false) return;

        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_options['server'];
        $phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
        $phpmailer->Port = $smtp_options['port'];
        $phpmailer->Username = $smtp_options['username'];
        $phpmailer->Password = $smtp_options['password'];

        // Additional settings…
        $phpmailer->SMTPSecure = $smtp_options['encryption']; // Choose SSL or TLS, if necessary for your server
        $phpmailer->From = $mail_options['address_from_email'];
        $phpmailer->FromName = $mail_options['address_from_name'];
    }
}