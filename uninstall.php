<?php

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

// Drop the Payments table
Slash\Database\Migrations::dropPaymentsTable();

// Clear plugin options
delete_option("coupons_plugin");

// Clear coupon data in posts
$posts = get_posts([
    'numberposts' => -1
]);
array_walk($posts, function ($post, $index) {
    delete_post_meta($post->ID, 'slash_coupon_data');
});