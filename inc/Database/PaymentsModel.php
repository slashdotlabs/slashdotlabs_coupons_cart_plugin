<?php
/**
* @package           CouponsCartPlugin
*/
namespace Slash\Database;

class PaymentsModel
{
	public function fetchPayments()
	{
		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM wp_payments" );
	}
}