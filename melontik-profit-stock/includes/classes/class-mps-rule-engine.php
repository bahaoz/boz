<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Rule_Engine {
	public function apply_rules( $order_data, array $channel_settings ) {
		$fees = array(
			'commission'      => 0.0,
			'service_fee'     => 0.0,
			'commission_vat'  => 0.0,
			'shipping_deduct' => 0.0,
			'payment_fee'     => 0.0,
			'coupon_cost'     => 0.0,
			'other_fee'       => 0.0,
			'promo_cost'      => 0.0,
		);

		MPS_Logger::log( 'Rule engine apply_rules skeleton', array( 'channel' => $channel_settings ) );

		return $fees;
	}
}
