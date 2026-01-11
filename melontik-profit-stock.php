<?php
/**
 * Plugin Name: Melontik Karlılık + Ortak Stok
 * Description: WooCommerce için kârlılık raporları, ürün maliyet alanları ve ileride pazaryeri/ortak stok altyapısı sağlar.
 * Version: 1.0.0
 * Author: Melontik
 * Text Domain: melontik-profit-stock
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MPS_VERSION', '1.0.0' );
define( 'MPS_PLUGIN_FILE', __FILE__ );
define( 'MPS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MPS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MPS_PLUGIN_DIR . 'includes/class-mps-plugin.php';

function mps_bootstrap() {
	return MPS_Plugin::instance();
}

add_action( 'plugins_loaded', 'mps_bootstrap' );
