<?php

namespace Slash\Base;


class SettingsLinks extends BaseController
{
    public function register()
    {
        add_filter("plugin_action_links_{$this->plugin_name}", [$this, 'settings_links']);
    }

    public function settings_links($links)
    {
        $settings_link = '<a href="admin.php?page=slash_coupon_plugin">Settings</a>';
        array_push($links, $settings_link);
        return $links;
    }
}