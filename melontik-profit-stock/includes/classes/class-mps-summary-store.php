<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Summary_Store implements MPS_Summary_Store_Interface {
	public function write_daily_summary( array $summary ) {
		MPS_Logger::log( 'Summary store write skeleton', $summary );
	}

	public function get_summary( $start_date, $end_date, $channel ) {
		MPS_Logger::log( 'Summary store read skeleton', array( 'start' => $start_date, 'end' => $end_date, 'channel' => $channel ) );

		return array();
	}
}
