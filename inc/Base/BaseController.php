<?php


namespace Slash\Base;


use Slash\Api\Twig;

abstract class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $plugin_name;
    public $twig;

    public $ccart_settings = array();
        
    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin_name = SLASH_COUPON_PLUGIN_NAME;


        $this->ccart_settings = array(
            'ipay' => 
            [
                'vendor_id' => ['Vendor ID',''],
                'hashkey' => ['Hash Key',''],
            ],
            'mail'=>
            [
                'address_from_name' => ['Sender Username','e.g. Coupons Cart'],
                'address_from_email' => ['Sender Email Address','e.g. couponscart@mail.com'],
            ],   
            'smtp'=>
            [
                'smtp_server' => ['SMTP Server','e.g. smtp.mail.com', 'text'],
                'smtp_port' => ['SMTP Port','', 'text'],
                'smtp_encryption'=>['SMTP Encryption','', 'option'],
                'smtp_username'=>['SMTP Username','e.g. admin@mail.com', 'text'],
                'smtp_password'=>['SMTP Password','', 'text']
            ]
        );

        $this->twig = Twig::instance();
    }

}