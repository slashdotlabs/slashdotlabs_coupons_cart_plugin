<?php


namespace Slash\Base;


use Slash\Api\Twig;

abstract class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $plugin_name;
    public $plugin_file;
    public $plugin_slug;
    public $twig;

    public $ccart_settings = array();

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path($this->dirname_r(__FILE__, 2));
        $this->plugin_url = plugin_dir_url($this->dirname_r(__FILE__, 2));
        $this->plugin_name = SLASH_COUPON_PLUGIN_NAME;

        $name_parts = explode(DIRECTORY_SEPARATOR, $this->plugin_name);
        $this->plugin_slug = $name_parts[0];
        $this->plugin_file = $this->plugin_path.$name_parts[1];

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
                'port' => ['SMTP Port','', 'number'],
                'encryption'=>['SMTP Encryption','', 'option'],
                'username'=>['SMTP Username','e.g. admin@mail.com', 'text'],
                'password'=>['SMTP Password','', 'password'],
            ]
        );

        $this->twig = Twig::instance();
    }

    // Backward compatibility for PHP < 7.0
    function dirname_r($path, $count=1){
        if ($count > 1){
            return dirname($this->dirname_r($path, --$count));
        }else{
            return dirname($path);
        }
    }

}