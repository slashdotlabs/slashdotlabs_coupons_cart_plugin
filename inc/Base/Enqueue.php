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

        // Only in post new and post edit page
        $query = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $query);
        $file = explode('?', end($parts))[0];
        if (
        in_array($file, ['post.php', 'post-new.php'])
        ) {
            wp_enqueue_style('slash-coupon-plugin-tailwind', $this->plugin_url . 'assets/css/slashcoupon.css');
        }


        // Only in payments page
        if (array_key_exists('page', $_GET) && $_GET['page'] === "ccart_pay") {

//            Datatable js
            wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.3.1.js');
            wp_enqueue_script("datatable-jquery","https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js");
            wp_enqueue_script("datatable-bootstrap","https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js");

//            Datatble css
            wp_enqueue_style("bootstrap", "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css");
            wp_enqueue_style("datatable-bootstrap", "https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css");

            wp_enqueue_script('custom-datatables', $this->plugin_url . 'assets/custom_datatables.js');
        }
    }

    public function enqueue_front_scripts()
    {
        if (is_single()) {
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