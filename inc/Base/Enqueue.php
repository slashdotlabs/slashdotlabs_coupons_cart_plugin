<?php


namespace Slash\Base;


class Enqueue extends BaseController
{
    public function register()
    {
        // admin
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

        // site
        add_action('wp_enqueue_scripts', [$this, 'enqueue_front_scripts'], 999);
    }

    public function enqueue_admin_scripts()
    {
        // enqueue all scripts
        wp_enqueue_style('slash-coupon-plugin-style', $this->plugin_url . 'assets/admin.css');
        wp_enqueue_script('slash-coupon-plugin-script', $this->plugin_url . 'assets/admin.js');

        $query = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $query);
        $file = explode('?', end($parts))[0];
        if (
        in_array($file, ['post.php', 'post-new.php'])
        ) {
            wp_enqueue_style('slash-coupon-plugin-tailwind', $this->plugin_url . 'assets/css/slashcoupon.css');
        }

        // datatables scripts
        wp_enqueue_style('dataTables-css', 'https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css');
        wp_enqueue_script('dataTables-js', 'https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js');
        wp_enqueue_script('custom-datatables', $this->plugin_url.'assets/custom_datatables.js');

    }

    public function enqueue_front_scripts()
    {
        if (get_the_ID()) {
            wp_enqueue_style('scp-tailwind', $this->plugin_url . 'assets/css/slashcoupon.css');

            // Masked Inputs
            wp_enqueue_script('scp-maskedinputs-script', $this->plugin_url . 'assets/js/jquery.maskedinput.min.js', ['jquery']);

            wp_enqueue_script('scp-main-script', $this->plugin_url . 'assets/js/main.js', [
                'jquery',
                'scp-maskedinputs-script'
            ]);

            $title_nonce = wp_create_nonce('scp_redeem_form');
            wp_localize_script('scp-main-script', 'redeem_form_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => $title_nonce
            ]);
        }

    }
}