<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface MPS_Summary_Store_Interface {
	public function write_daily_summary( array $summary );

	public function get_summary( $start_date, $end_date, $channel );
}
