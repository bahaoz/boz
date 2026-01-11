<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Hepsiburada_Client implements MPS_Marketplace_Client_Interface {
	public function fetch_orders( $start_date, $end_date ) {
		MPS_Logger::log( 'Hepsiburada API fetch requested (skeleton)', array( 'start' => $start_date, 'end' => $end_date ) );

		return array();
	}
}
