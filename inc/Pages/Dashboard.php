<?php


namespace Slash\Pages;


use Slash\Api\Callbacks\AdminCallbacks;
use Slash\Api\SettingsApi;
use Slash\Base\BaseController;

class Dashboard extends BaseController
{
    public $settingsApi;
    public $pages = [];
    public $callbacks;

    public function __construct()
    {
        parent::__construct();
        $this->settingsApi = new SettingsApi();
        $this->callbacks = new AdminCallbacks();
    }

    public function register()
    {
//        $this->setSettings();
//        $this->setSections();
//        $this->setFields();
        $this->setPages();
        $this->settingsApi
            ->addPages($this->pages)->withSubPage('Dashboard')
            ->register();
    }

    public function setPages()
    {
        $this->pages = [
            [
                'page_title' => 'Slash Coupons Cart',
                'menu_title' => 'Slash Coupons',
                'capability' => 'manage_options',
                'menu_slug' => 'slash_coupon_plugin',
                'callback' => [$this->callbacks, 'adminDashboard'],
                'icon_url' => 'dashicons-tickets-alt',
                'position' => 110
            ]
        ];
    }

//    public function setSettings()
//    {
//        // ?option name should be similar to page name
//        $args = [
//            [
//                'option_group' => 'slash_coupon_settings',
//                'option_name' => 'slash_coupon_plugin',
//                'callback' => [$this->callbacks, 'checkboxSanitize']
//            ]
//        ];
//        $this->settingsApi->setSettings($args);
//    }
//
//    public function setSections()
//    {
//        $args = [
//            [
//                'id' => 'slash_coupon_index',
//                'title' => 'Settings Manager',
//                'callback' => [$this->callbacks_manager, 'adminSectionManager'],
//                'page' => 'alecadd_plugin'
//            ]
//        ];
//        $this->settingsApi->setSections($args);
//    }
//
//    public function setFields()
//    {
//        $args = array_map(function ($manager, $title) {
//            return [
//                'id' => $manager,
//                'title' => $title,
//                'callback' => [$this->callbacks_manager, 'checkboxField'],
//                'page' => 'alecadd_plugin',
//                'section' => 'alecadd_admin_index',
//                'args' => [
//                    'option_name' => 'alecadd_plugin',
//                    'label_for' => $manager,
//                    'classes' => 'ui-toggle'
//                ]
//            ];
//        }, array_keys($this->managers), array_values($this->managers));
//        $this->settingsApi->setFields($args);
//    }
}