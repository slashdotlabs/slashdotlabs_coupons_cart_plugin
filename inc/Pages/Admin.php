<?php
namespace Slash\Pages;

use \Slash\Base\BaseController;
use \Slash\Api\SettingsApi;
use \Slash\Api\Callbacks\AdminCallbacks;

class Admin extends BaseController {

    public $settings;
    public $callbacks;

    public $pages = array();
    public $subpages = array();

    public function setPages(){
        
        $this->pages = array(
            [
                'page_title' => 'Coupons Cart',
                'menu_title' => 'Coupons Cart', 
                'capability' => 'manage_options' ,
                'menu_slug' => 'coupons_plugin', 
                'callback' => array( $this->callbacks, 'confSettings'),
                'icon_url' => 'dashicons-cart',
                'position' => 80
            ],
        );
    }
    public function setSubPages(){
        $this->subpages = array(
            [
                'parent_slug' => 'coupons_plugin',
                'page_title' => 'Coupon Payments',
                'menu_title' => 'Payments',
                'capability' => 'manage_options',
                'menu_slug' => 'ccart_pay',
                'callback' => array( $this->callbacks, 'paymentsPage'),

            ]
        );
    }
    public function register(){

        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();

        $this->setPages();
        $this->setSubPages();

        $this->settings->addPages( $this->pages )
                        ->withSubPage('Configuration Settings')
                        ->addSubPages( $this->subpages)
                        ->register();
    }
 

}