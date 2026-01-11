<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Logger {
	public static function log( $message, $context = array() ) {
		if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
			return;
		}

		$entry = '[MPS] ' . $message;

		if ( ! empty( $context ) ) {
			$entry .= ' | ' . wp_json_encode( $context );
		}

		error_log( $entry ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	}
}
