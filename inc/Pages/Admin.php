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
        //coupon settings array
        $coupon_settings = array(
            [
                'option_group' => 'ccart_admin_group',
                'option_name' => 'vendor_id',
                'callback' => array($this->callbacks, 'ccartAdminGroup'),

            ],
            [
                'option_group' => 'ccart_admin_group',
                'option_name' => 'hashkey',
                'callback' => array($this->callbacks, 'ccartAdminGroup'),

            ],
            [
                'option_group' => 'ccart_admin_group',
                'option_name' => 'address_from_name',
                'callback' => array($this->callbacks, 'ccartAdminGroup'),

            ],
            [
                'option_group' => 'ccart_admin_group',
                'option_name' => 'address_from_email',
                'callback' => array($this->callbacks, 'ccartAdminGroup'),

            ],

        );
        $this->settings->setSettings($coupon_settings);
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
        $args = array(
            [
                'id' => 'vendor_id',
                'title' => 'Vendor ID',
                'callback' => array($this->callbacks, 'ccartVendorId'),
                'page' => 'coupons_plugin',
                'section' => 'ccart_ipay_index',
                'args' => array(
                    'label_for' => 'vendor_id',
                    'class' => 'example-class',

                )

            ],
            [
                'id' => 'hashkey',
                'title' => 'Hash Key',
                'callback' => array($this->callbacks, 'ccartHashkey'),
                'page' => 'coupons_plugin',
                'section' => 'ccart_ipay_index',
                'args' => array(
                    'label_for' => 'hashkey',
                    'class' => 'example-class',

                )

            ],
            [
                'id' => 'address_from_name',
                'title' => 'Sender Username',
                'callback' => array($this->callbacks, 'ccartAddressFromName'),
                'page' => 'coupons_plugin',
                'section' => 'ccart_mail_index',
                'args' => array(
                    'label_for' => 'address_from_name',
                    'class' => 'example-class',

                )

            ],
            [
                'id' => 'address_from_email',
                'title' => 'Sender Email Address',
                'callback' => array($this->callbacks, 'ccartAddressFromEmail'),
                'page' => 'coupons_plugin',
                'section' => 'ccart_mail_index',
                'args' => array(
                    'label_for' => 'address_from_email',
                    'class' => 'example-class',

                )

            ],
        );
        $this->settings->setFields($args);
    }
}