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
}
