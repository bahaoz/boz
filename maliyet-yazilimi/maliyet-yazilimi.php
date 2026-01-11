<?php
/**
 * Plugin Name: Maliyet Yazılımı
 * Description: WooCommerce için maliyet ve kârlılık yönetimi eklentisi.
 * Version: 1.0.0
 * Author: Barugu
 * Text Domain: maliyet-yazilimi
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MYS_VERSION', '1.0.0' );
define( 'MYS_PLUGIN_FILE', __FILE__ );
define( 'MYS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MYS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MYS_PLUGIN_DIR . 'includes/class-mys-plugin.php';

function mys_bootstrap() {
	return MYS_Plugin::instance();
}

add_action( 'plugins_loaded', 'mys_bootstrap' );
