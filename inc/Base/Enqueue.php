<?php


namespace Slash\Base;


class Enqueue extends BaseController
{
    public function register()
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function enqueue_admin_scripts()
    {
        // enqueue all scripts
        wp_enqueue_style('slash-coupon-plugin-style', $this->plugin_url.'assets/admin.css');
        wp_enqueue_script('slash-coupon-plugin-script', $this->plugin_url.'assets/admin.js');
    }

}