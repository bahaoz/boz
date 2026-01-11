<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once MPS_PLUGIN_DIR . 'includes/classes/class-mps-logger.php';
require_once MPS_PLUGIN_DIR . 'includes/classes/class-mps-calculator.php';
require_once MPS_PLUGIN_DIR . 'includes/classes/class-mps-order-repository.php';
require_once MPS_PLUGIN_DIR . 'includes/classes/interface-mps-summary-store.php';
require_once MPS_PLUGIN_DIR . 'includes/classes/class-mps-summary-store.php';
require_once MPS_PLUGIN_DIR . 'includes/classes/class-mps-rule-engine.php';
require_once MPS_PLUGIN_DIR . 'includes/marketplaces/interface-mps-importer.php';
require_once MPS_PLUGIN_DIR . 'includes/marketplaces/interface-mps-marketplace-client.php';
require_once MPS_PLUGIN_DIR . 'includes/marketplaces/class-mps-csv-importer.php';
require_once MPS_PLUGIN_DIR . 'includes/marketplaces/class-mps-trendyol-client.php';
require_once MPS_PLUGIN_DIR . 'includes/marketplaces/class-mps-hepsiburada-client.php';
require_once MPS_PLUGIN_DIR . 'admin/class-mps-admin.php';
require_once MPS_PLUGIN_DIR . 'admin/class-mps-product-fields.php';
require_once MPS_PLUGIN_DIR . 'admin/class-mps-order-meta.php';

class MPS_Plugin {
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

		new MPS_Admin();
		new MPS_Product_Fields();
		new MPS_Order_Meta();

		add_action( 'woocommerce_checkout_create_order', array( $this, 'set_default_order_channel' ), 10, 2 );
	}

	public function set_default_order_channel( $order, $data ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		if ( ! $order->get_meta( '_mps_channel', true ) ) {
			$order->update_meta_data( '_mps_channel', 'woo' );
		}
	}
}
