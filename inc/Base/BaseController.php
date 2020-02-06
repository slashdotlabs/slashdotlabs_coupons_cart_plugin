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
                'live' => ['Set iPay to Live', '', 'toggle'],
                'vendor_id' => ['Vendor ID','', 'text'],
                'hashkey' => ['Hash Key','', 'text'],
                
            ],
            'mail'=>
            [
                'address_from_name' => ['Sender Username','e.g. Coupons Cart'],
                'address_from_email' => ['Sender Email Address','e.g. couponscart@mail.com'],
            ],   
            'smtp'=>
            [
                'settings' => ['Set SMTP Settings', '', 'toggle'],
                'server' => ['SMTP Server','e.g. smtp.mail.com', 'text'],
                'port' => ['SMTP Port','', 'text'],
                'encryption'=>['SMTP Encryption','', 'option'],
                'username'=>['SMTP Username','e.g. admin@mail.com', 'text'],
                'password'=>['SMTP Password','', 'password'],
            ]
        );

        $this->twig = Twig::instance();
    }

}