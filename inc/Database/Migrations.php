<?php
/**
 * @package           CouponsCartPlugin
 */

namespace Slash\Database;

class Migrations
{

    /**
     * Creates the Payments Table
     */
    public static function createPaymentsTable()
    {
        global $table_prefix, $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $tblname = 'payments';
        $wp_payments_table = $table_prefix . "$tblname ";

        $sql = "CREATE TABLE $wp_payments_table(
        id int AUTO_INCREMENT NOT NULL,
        fname varchar(125) NOT NULL,
        lname varchar(125) NOT NULL,
        email varchar(125) NOT NULL,
        phone varchar(125) NOT NULL,
        order_id varchar(255) NOT NULL,
        coupon_id int NOT NULL,
        customer_coupon varchar(255) NULL,
        amount double(8,2) NOT NULL,
        transaction_ref varchar(125) NULL,
        payment_type varchar(125) NULL,
        payment_date timestamp NULL,
        additional_information text NULL,
        status ENUM('initiated', 'cancelled', 'completed') DEFAULT 'initiated',
        PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Drops the Payment Table
     */
    public static function dropPaymentsTable()
    {
        global $wpdb, $table_prefix;

        $payments_table = $table_prefix."payments";

        $wpdb->query("DROP TABLE IF EXISTS $payments_table");
    }
}