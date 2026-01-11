<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface MPS_Marketplace_Client_Interface {
	public function fetch_orders( $start_date, $end_date );
}
