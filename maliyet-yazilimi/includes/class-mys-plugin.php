<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once MYS_PLUGIN_DIR . 'includes/classes/class-mys-logger.php';
require_once MYS_PLUGIN_DIR . 'admin/class-mys-admin.php';

class MYS_Plugin {
	private static $instance;

	private function __construct() {
		$this->init();
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		new MYS_Admin();
	}
}
