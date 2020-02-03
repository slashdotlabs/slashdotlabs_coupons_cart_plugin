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

        // datatables scripts
        wp_enqueue_style('dataTables-css', 'https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css');
        wp_enqueue_script('dataTables-js', 'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js');
        wp_enqueue_script('custom-datatables', $this->plugin_url.'assets/custom_datatables.js');

    }

}