<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Order_Repository {
	public function get_orders( $start_date, $end_date, $channel ) {
		$args = array(
			'status'       => array( 'processing', 'completed', 'on-hold' ),
			'limit'        => -1,
			'orderby'      => 'date',
			'order'        => 'DESC',
			'date_created' => $this->build_date_query( $start_date, $end_date ),
		);

		if ( $channel && 'woo' !== $channel ) {
			$args['meta_key']   = '_mps_channel';
			$args['meta_value'] = $channel;
		}

		return wc_get_orders( $args );
	}

	private function build_date_query( $start_date, $end_date ) {
		$range = array();

		if ( $start_date ) {
			$range['after'] = $start_date . ' 00:00:00';
		}

		if ( $end_date ) {
			$range['before'] = $end_date . ' 23:59:59';
		}

		return $range;
	}
}
