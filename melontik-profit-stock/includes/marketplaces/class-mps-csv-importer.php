<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_CSV_Importer implements MPS_Importer_Interface {
	public function import( $file_path ) {
		MPS_Logger::log( 'CSV import requested (skeleton)', array( 'file' => $file_path ) );

		return array(
			'success' => false,
			'message' => __( 'CSV import altyapısı v2 ile tamamlanacaktır.', 'melontik-profit-stock' ),
		);
	}

	public function get_template_fields() {
		return array(
			'order_number',
			'order_date',
			'channel',
			'gross_incl_tax',
			'tax_total',
			'shipping_collected',
			'discount_total',
			'commission_fee',
			'service_fee',
			'commission_vat',
			'shipping_deduction',
			'coupon_cost',
			'other_fee',
			'payment_fee',
			'promo_cost',
		);
	}
}
