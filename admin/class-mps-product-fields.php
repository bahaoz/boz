<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Product_Fields {
	public function __construct() {
		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_profit_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'render_profit_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_simple_product_fields' ) );

		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'render_variation_fields' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_variation_fields' ), 10, 2 );
	}

	public function add_profit_tab( $tabs ) {
		$tabs['mps_profit'] = array(
			'label'    => __( 'Karlılık', 'melontik-profit-stock' ),
			'target'   => 'mps_profit_panel',
			'class'    => array(),
			'priority' => 70,
		);

		return $tabs;
	}

	public function render_profit_panel() {
		global $post;
		?>
		<div id="mps_profit_panel" class="panel woocommerce_options_panel">
			<div class="options_group">
				<?php
				woocommerce_wp_text_input(
					array(
						'id'                => '_mps_purchase_cost',
						'label'             => __( 'Alış Fiyatı', 'melontik-profit-stock' ),
						'placeholder'       => '0.00',
						'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
						'desc_tip'          => true,
						'description'       => __( 'Ürün başına zorunlu maliyet.', 'melontik-profit-stock' ),
						'value'             => get_post_meta( $post->ID, '_mps_purchase_cost', true ),
					)
				);
				woocommerce_wp_text_input(
					array(
						'id'                => '_mps_packaging_cost',
						'label'             => __( 'Ambalaj Maliyeti', 'melontik-profit-stock' ),
						'placeholder'       => '0.00',
						'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
						'value'             => get_post_meta( $post->ID, '_mps_packaging_cost', true ),
					)
				);
				woocommerce_wp_text_input(
					array(
						'id'                => '_mps_fixed_overhead',
						'label'             => __( 'Sabit Gider Payı', 'melontik-profit-stock' ),
						'placeholder'       => '0.00',
						'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
						'value'             => get_post_meta( $post->ID, '_mps_fixed_overhead', true ),
					)
				);
				?>
			</div>
		</div>
		<?php
	}

	public function save_simple_product_fields( $post_id ) {
		if ( isset( $_POST['_mps_purchase_cost'] ) ) {
			update_post_meta( $post_id, '_mps_purchase_cost', wc_format_decimal( wp_unslash( $_POST['_mps_purchase_cost'] ) ) );
		}

		if ( isset( $_POST['_mps_packaging_cost'] ) ) {
			update_post_meta( $post_id, '_mps_packaging_cost', wc_format_decimal( wp_unslash( $_POST['_mps_packaging_cost'] ) ) );
		}

		if ( isset( $_POST['_mps_fixed_overhead'] ) ) {
			update_post_meta( $post_id, '_mps_fixed_overhead', wc_format_decimal( wp_unslash( $_POST['_mps_fixed_overhead'] ) ) );
		}
	}

	public function render_variation_fields( $loop, $variation_data, $variation ) {
		woocommerce_wp_text_input(
			array(
				'id'                => "_mps_purchase_cost_{$loop}",
				'name'              => "_mps_purchase_cost[{$loop}]",
				'label'             => __( 'Alış Fiyatı', 'melontik-profit-stock' ),
				'wrapper_class'     => 'form-row form-row-first',
				'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
				'value'             => get_post_meta( $variation->ID, '_mps_purchase_cost', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => "_mps_packaging_cost_{$loop}",
				'name'              => "_mps_packaging_cost[{$loop}]",
				'label'             => __( 'Ambalaj Maliyeti', 'melontik-profit-stock' ),
				'wrapper_class'     => 'form-row form-row-last',
				'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
				'value'             => get_post_meta( $variation->ID, '_mps_packaging_cost', true ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'                => "_mps_fixed_overhead_{$loop}",
				'name'              => "_mps_fixed_overhead[{$loop}]",
				'label'             => __( 'Sabit Gider Payı', 'melontik-profit-stock' ),
				'wrapper_class'     => 'form-row form-row-full',
				'custom_attributes' => array( 'step' => '0.01', 'min' => '0' ),
				'value'             => get_post_meta( $variation->ID, '_mps_fixed_overhead', true ),
			)
		);
	}

	public function save_variation_fields( $variation_id, $loop ) {
		if ( isset( $_POST['_mps_purchase_cost'][ $loop ] ) ) {
			update_post_meta( $variation_id, '_mps_purchase_cost', wc_format_decimal( wp_unslash( $_POST['_mps_purchase_cost'][ $loop ] ) ) );
		}

		if ( isset( $_POST['_mps_packaging_cost'][ $loop ] ) ) {
			update_post_meta( $variation_id, '_mps_packaging_cost', wc_format_decimal( wp_unslash( $_POST['_mps_packaging_cost'][ $loop ] ) ) );
		}

		if ( isset( $_POST['_mps_fixed_overhead'][ $loop ] ) ) {
			update_post_meta( $variation_id, '_mps_fixed_overhead', wc_format_decimal( wp_unslash( $_POST['_mps_fixed_overhead'][ $loop ] ) ) );
		}
	}
}
