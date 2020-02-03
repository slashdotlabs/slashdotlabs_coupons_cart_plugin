<?php


namespace Slash\Base;


abstract class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $plugin_name;

    public $ccart_settings = array();
        
    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin_name = SLASH_COUPON_PLUGIN_NAME;


        $this->ccart_settings = array(
            'ipay' => 
            [
                'vendor_id' => ['Vendor ID', 'e.g. VID-001'],
                'hashkey' => ['Hash Key', 'e.g. #01-29-90'],
            ],
            'mail'=>
            [
                'address_from_name' => ['Sender Username','e.g. Coupons Cart'],
                'address_from_email' => ['Sender Email Address','e.g. couponscart@mail.com'],
            ],   
        );

    }

}