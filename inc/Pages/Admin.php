<?php
namespace Slash\Pages;

use \Slash\Base\BaseController;
use \Slash\Api\SettingsApi;

class Admin extends BaseController {

    public $settings;

    public $pages = array();

    public function __construct(){
        $this->settings = new SettingsApi();
        $this->pages = array(
            [
                'page_title' => 'Coupons Cart',
                'menu_title' => 'Coupons Cart', 
                'capability' => 'manage_options' ,
                'menu_slug' => 'coupons_plugin', 
                'callback' => function() {echo '<h1> Coupons Cart Settings Page </h1>'; },
                'icon_url' => 'dashicons-cart',
                'position' => 80
            ],
        );
    }
    public function register(){
        $this->settings->addPages( $this->pages )->register();
    }
 

}