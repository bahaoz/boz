<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MYS_Admin {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	public function register_menu() {
		add_menu_page(
			__( 'Maliyet Yazılımı', 'maliyet-yazilimi' ),
			__( 'Maliyet Yazılımı', 'maliyet-yazilimi' ),
			'manage_woocommerce',
			'mys-dashboard',
			array( $this, 'render_dashboard' ),
			'dashicons-analytics'
		);
	}

	public function render_dashboard() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		require MYS_PLUGIN_DIR . 'admin/views/dashboard.php';
	}
}
