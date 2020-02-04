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

    public function setSettings()
    {
        $args = array(
            array(
                'option_group' => 'ccart_plugin_settings',
                'option_name' => 'coupons_plugin',
                'callback' => array($this->callbacks, 'ccartSettingsSanitize')
            )
        );
        $this->settings->setSettings($args);
    }

    public function setSections()
    {
        $args = array(
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

        );
        $this->settings->setSections($args);

    }

    public function setFields()
    {
        $args = array();
        foreach ($this->ccart_settings['ipay'] as $key => $value) {
            $args[] = array(
                'id' => $key,
                'title' => $value[0],
                'callback' => array($this->callbacks, 'ccartSettingsFields'),
                'page' => 'coupons_plugin',
                'section' => 'ccart_ipay_index',
                'args' => array(
                    'label_for' => $key,
                    'placeholder' => $value[1],
                    'class' => 'example-class',
                    'field' => 'ipay',
                    'option_name' => 'coupons_plugin',
                )
            );
        }
        foreach ($this->ccart_settings['mail'] as $key => $value) {
            $args[] = array(
                'id' => $key,
                'title' => $value[0],
                'callback' => array($this->callbacks, 'ccartSettingsFields'),
                'page' => 'coupons_plugin',
                'section' => 'ccart_mail_index',
                'args' => array(
                    'label_for' => $key,
                    'placeholder' => $value[1],
                    'field' => 'mail',
                    'class' => 'example-class',
                    'option_name' => 'coupons_plugin',
                )
            );
        }
        $this->settings->setFields($args);
    }

}