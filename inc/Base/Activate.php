<?php


namespace Slash\Base;


class Activate
{
    public static function run()
    {
        flush_rewrite_rules();
        return (new self)->createPaymentsTable();
    }

    /**
     * creates the Payments submission table
     */
    public function createPaymentsTable()
    {
    	global $table_prefix, $wpdb;

    	$charset_collate = $wpdb->get_charset_collate();

	    $tblname = 'payments';
	    $wp_payments_table = $table_prefix . "$tblname ";

	    // Check to see if the table exists already, if not, then create it

	    if($wpdb->get_var( "show tables like '$wp_payments_table'" ) != $wp_payments_table) 
	    {
	    	$sql =  "CREATE TABLE $wp_payments_table(
	    	id int NOT NULL,
	    	fname varchar(255) NOT NULL,
	    	lname varchar(255) NOT NULL,
	    	email varchar(255) NOT NULL,
	    	phone varchar(255) NOT NULL,
	    	order_id int NOT NULL,
	    	coupon_id int NOT NULL,
	    	amount int NOT NULL,
	    	transaction_ref varchar(255),
	    	payment_type varchar(255),
	    	payment_date datetime,
	    	status varchar(125),
	    	PRIMARY KEY  (id)
	    	) $charset_collate;";

	        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	        dbDelta($sql);
	    }
    }
}