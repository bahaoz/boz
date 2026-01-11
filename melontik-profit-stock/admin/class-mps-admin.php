<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Admin {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_menu() {
		add_menu_page(
			__( 'Karlılık', 'melontik-profit-stock' ),
			__( 'Karlılık', 'melontik-profit-stock' ),
			'manage_woocommerce',
			'mps-profit',
			array( $this, 'render_dashboard' ),
			'dashicons-chart-line'
		);

		add_submenu_page(
			'mps-profit',
			__( 'Raporlar', 'melontik-profit-stock' ),
			__( 'Raporlar', 'melontik-profit-stock' ),
			'manage_woocommerce',
			'mps-profit',
			array( $this, 'render_dashboard' )
		);

		add_submenu_page(
			'mps-profit',
			__( 'Ayarlar', 'melontik-profit-stock' ),
			__( 'Ayarlar', 'melontik-profit-stock' ),
			'manage_woocommerce',
			'mps-settings',
			array( $this, 'render_settings' )
		);

		add_submenu_page(
			'mps-profit',
			__( 'Pazaryeri Import', 'melontik-profit-stock' ),
			__( 'Pazaryeri Import', 'melontik-profit-stock' ),
			'manage_woocommerce',
			'mps-import',
			array( $this, 'render_import' )
		);
	}

	public function register_settings() {
		register_setting( 'mps_settings_group', 'mps_settings', array( $this, 'sanitize_settings' ) );

		add_settings_section(
			'mps_settings_main',
			__( 'Genel Ayarlar', 'melontik-profit-stock' ),
			'__return_false',
			'mps_settings'
		);

		add_settings_field(
			'shipping_cost_method',
			__( 'Varsayılan Kargo Maliyeti', 'melontik-profit-stock' ),
			array( $this, 'render_shipping_method_field' ),
			'mps_settings',
			'mps_settings_main'
		);

		add_settings_field(
			'default_shipping_cost',
			__( 'Sabit Kargo Maliyeti (TL)', 'melontik-profit-stock' ),
			array( $this, 'render_default_shipping_cost_field' ),
			'mps_settings',
			'mps_settings_main'
		);

		add_settings_field(
			'default_payment_fee_rate',
			__( 'Varsayılan Ödeme Kesintisi (%)', 'melontik-profit-stock' ),
			array( $this, 'render_payment_fee_field' ),
			'mps_settings',
			'mps_settings_main'
		);
	}

	public function sanitize_settings( $input ) {
		$sanitized = array();

		$sanitized['shipping_cost_method'] = isset( $input['shipping_cost_method'] ) ? sanitize_text_field( $input['shipping_cost_method'] ) : 'manual';
		$sanitized['default_shipping_cost'] = isset( $input['default_shipping_cost'] ) ? (float) $input['default_shipping_cost'] : 0;
		$sanitized['default_payment_fee_rate'] = isset( $input['default_payment_fee_rate'] ) ? (float) $input['default_payment_fee_rate'] : 0;

		return $sanitized;
	}

	public function render_shipping_method_field() {
		$settings = get_option( 'mps_settings', array() );
		$method   = isset( $settings['shipping_cost_method'] ) ? $settings['shipping_cost_method'] : 'manual';
		?>
		<select name="mps_settings[shipping_cost_method]">
			<option value="manual" <?php selected( $method, 'manual' ); ?>><?php esc_html_e( 'Manuel', 'melontik-profit-stock' ); ?></option>
			<option value="fixed" <?php selected( $method, 'fixed' ); ?>><?php esc_html_e( 'Sabit', 'melontik-profit-stock' ); ?></option>
		</select>
		<?php
	}

	public function render_default_shipping_cost_field() {
		$settings = get_option( 'mps_settings', array() );
		$value    = isset( $settings['default_shipping_cost'] ) ? $settings['default_shipping_cost'] : '';
		?>
		<input type="number" step="0.01" name="mps_settings[default_shipping_cost]" value="<?php echo esc_attr( $value ); ?>" />
		<?php
	}

	public function render_payment_fee_field() {
		$settings = get_option( 'mps_settings', array() );
		$value    = isset( $settings['default_payment_fee_rate'] ) ? $settings['default_payment_fee_rate'] : '';
		?>
		<input type="number" step="0.01" name="mps_settings[default_payment_fee_rate]" value="<?php echo esc_attr( $value ); ?>" />
		<?php
	}

	public function render_dashboard() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		require MPS_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	public function render_settings() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		require MPS_PLUGIN_DIR . 'admin/views/settings.php';
	}

	public function render_import() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		require MPS_PLUGIN_DIR . 'admin/views/coming-soon.php';
	}
}
