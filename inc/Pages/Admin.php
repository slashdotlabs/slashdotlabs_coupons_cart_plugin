<?php

namespace Slash\Pages;

use Slash\Api\Callbacks\AdminCallbacks;
use Slash\Api\SettingsApi;
use Slash\Base\BaseController;

class Admin extends BaseController
{

    public $settings;
    public $callbacks;

    public $pages = array();
    public $subpages = array();

    public function __construct()
    {
        parent::__construct();
        $this->settings = new SettingsApi();
        $this->callbacks = new AdminCallbacks();
    }

    public function register()
    {
        $this->setPages();
        $this->setSubPages();

        $this->setSettings();
        $this->setSections();
        $this->setFields();
        
        $this->settings->addPages($this->pages)
            ->withSubPage('Configuration Settings')
            ->addSubPages($this->subpages)
            ->register();
    }

    public function setPages()
    {

        $this->pages = array(
            [
                'page_title' => 'Coupons Cart',
                'menu_title' => 'Coupons Cart',
                'capability' => 'manage_options',
                'menu_slug' => 'coupons_plugin',
                'callback' => array($this->callbacks, 'confSettings'),
                'icon_url' => 'dashicons-cart',
                'position' => 80
            ],
        );
    }

    public function setSubPages()
    {
        $this->subpages = array(
            [
                'parent_slug' => 'coupons_plugin',
                'page_title' => 'Coupon Payments',
                'menu_title' => 'Payments',
                'capability' => 'manage_options',
                'menu_slug' => 'ccart_pay',
                'callback' => array($this->callbacks, 'paymentsPage'),

            ]
        );
    }

    
    public function setSettings(){
        $args = array(
            array(
                'option_group' => 'ccart_plugin_settings',
                'option_name' => 'coupons_plugin',
                'callback' => array( $this->callbacks, 'ccartSettingsValidate')
            )
        );
    $this->settings->setSettings($args);
}

    public function setSections()
    {
        $args = array(
            [
                'id' => 'ccart_ipay_live_index',
                'page' => 'coupons_plugin'

            ],
            [
                'id' => 'ccart_ipay_index',
                'title' => 'iPay Settings',
                'callback' => array($this->callbacks, 'ccartIpaySection'),
                'page' => 'coupons_plugin'

            ],
            [
                'id' => 'ccart_mail_index',
                'title' => 'Mailing Settings',
                'callback' => array($this->callbacks, 'ccartMailSection'),
                'page' => 'coupons_plugin'

            ],
            [
                'id' => 'ccart_set_smtp_index',
                'page' => 'coupons_plugin'

            ],
            [
                'id' => 'ccart_smtp_index',
                'title' => 'SMTP Settings',
                'callback' => array($this->callbacks, 'ccartSMTPSection'),
                'page' => 'coupons_plugin'

            ],

        );
        $this->settings->setSections($args);

    }

    public function setFields()
    {
        $args = array();
        foreach ($this->ccart_settings['ipay'] as $key => $value) {
            switch($value[2]){
                case "toggle":
                    $args[] = array(
                        'id' => $key,
                        'title' => $value[0],
                        'callback' => array( $this->callbacks, 'ccartCheckboxFields'),
                        'page' => 'coupons_plugin',
                        'section' => 'ccart_ipay_live_index',
                        'args' => array(
                            'label_for' => $key,
                            'field' => 'ipay',
                            'option_name' => 'coupons_plugin',
                            'class' =>'example-class',
                        )
                    );
                break;
                default:
                $args[] = array(
                    'id' => $key,
                    'title' => $value[0],
                    'callback' => array($this->callbacks, 'ccartTextFields'),
                    'page' => 'coupons_plugin',
                    'section' => 'ccart_ipay_index',
                    'args' => array(
                        'label_for' => $key,
                        'placeholder'=> $value[1],
                        'field' => 'ipay',
                        'class' =>'example-class',
                        'option_name' => 'coupons_plugin',    
                    )
                );
            }
        }
        foreach ($this->ccart_settings['mail'] as $key => $value) {
            $args[] = array(
                'id' => $key,
                'title' => $value[0],
                'callback' => array($this->callbacks, 'ccartTextFields'),
                'page' => 'coupons_plugin',
                'section' => 'ccart_mail_index',
                'args' => array(
                    'label_for' => $key,
                    'field' => 'mail',
                    'placeholder'=> $value[1],
                    'option_name' => 'coupons_plugin',
                    'class' =>'example-class',
                )
            );
        }
        
        foreach( $this->ccart_settings['smtp'] as $key => $value ){
            switch ($value[2]){
                case "toggle":
                    $args[] = array(
                        'id' => $key,
                        'title' => $value[0],
                        'callback' => array( $this->callbacks, 'ccartCheckboxFields'),
                        'page' => 'coupons_plugin',
                        'section' => 'ccart_set_smtp_index',
                        'args' => array(
                            'label_for' => $key,
                            'field' => 'smtp',
                            'option_name' => 'coupons_plugin',
                            'class' =>'example-class',
                        )
                    );
                break;  
                case "option":
                    $args[] = array(
                        'id' => $key,
                        'title' => $value[0],
                        'callback' => array( $this->callbacks, 'ccartRadioButtons'),
                        'page' => 'coupons_plugin',
                        'section' => 'ccart_smtp_index',
                        'args' => array(
                            'label_for' => $key,
                            'field' => 'smtp',
                            'placeholder'=> $value[1],
                            'option_name' => 'coupons_plugin',
                            'class' =>'example-class',
                        )
                    );
                break;
                case "password":
                    $args[] = array(
                        'id' => $key,
                        'title' => $value[0],
                        'callback' => array( $this->callbacks, 'ccartPasswordFields'),
                        'page' => 'coupons_plugin',
                        'section' => 'ccart_smtp_index',
                        'args' => array(
                            'label_for' => $key,
                            'field' => 'smtp',
                            'placeholder'=> $value[1],
                            'option_name' => 'coupons_plugin',
                            'class' =>'example-class',
                        )
                    );
                break;  
                default:
                    $args[] = array(
                        'id' => $key,
                        'title' => $value[0],
                        'callback' => array( $this->callbacks, 'ccartTextFields'),
                        'page' => 'coupons_plugin',
                        'section' => 'ccart_smtp_index',
                        'args' => array(
                            'label_for' => $key,
                            'field' => 'smtp',
                            'placeholder'=> $value[1],
                            'option_name' => 'coupons_plugin',
                            'class' =>'example-class',
                        )
                    );   
            }
        }
        $this->settings->setFields($args);
    }

}