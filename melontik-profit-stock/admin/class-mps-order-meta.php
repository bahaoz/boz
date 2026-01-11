<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Order_Meta {
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_box' ) );
		add_action( 'save_post_shop_order', array( $this, 'save_meta_box' ), 10, 2 );
	}

	public function register_meta_box() {
		add_meta_box(
			'mps_order_costs',
			__( 'Karlılık Maliyetleri', 'melontik-profit-stock' ),
			array( $this, 'render_meta_box' ),
			'shop_order',
			'side',
			'default'
		);
	}

	public function render_meta_box( $post ) {
		$order = wc_get_order( $post->ID );
		if ( ! $order ) {
			return;
		}

		wp_nonce_field( 'mps_order_meta', 'mps_order_meta_nonce' );

		$shipping_cost = $order->get_meta( '_mps_shipping_cost', true );
		$payment_fee   = $order->get_meta( '_mps_payment_fee', true );
		$commission    = $order->get_meta( '_mps_commission_fee', true );
		?>
		<p>
			<label for="mps_shipping_cost"><strong><?php esc_html_e( 'Kargo Maliyeti (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_shipping_cost" id="mps_shipping_cost" value="<?php echo esc_attr( $shipping_cost ); ?>" />
		</p>
		<p>
			<label for="mps_payment_fee"><strong><?php esc_html_e( 'Ödeme Kesintisi (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_payment_fee" id="mps_payment_fee" value="<?php echo esc_attr( $payment_fee ); ?>" />
		</p>
		<p>
			<label for="mps_commission_fee"><strong><?php esc_html_e( 'Komisyon (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_commission_fee" id="mps_commission_fee" value="<?php echo esc_attr( $commission ); ?>" />
		</p>
		<?php
	}

	public function save_meta_box( $post_id, $post ) {
		if ( 'shop_order' !== $post->post_type ) {
			return;
		}

		if ( ! isset( $_POST['mps_order_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mps_order_meta_nonce'] ) ), 'mps_order_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_shop_order', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['mps_shipping_cost'] ) ) {
			update_post_meta( $post_id, '_mps_shipping_cost', wc_format_decimal( wp_unslash( $_POST['mps_shipping_cost'] ) ) );
		}

		if ( isset( $_POST['mps_payment_fee'] ) ) {
			update_post_meta( $post_id, '_mps_payment_fee', wc_format_decimal( wp_unslash( $_POST['mps_payment_fee'] ) ) );
		}

		if ( isset( $_POST['mps_commission_fee'] ) ) {
			update_post_meta( $post_id, '_mps_commission_fee', wc_format_decimal( wp_unslash( $_POST['mps_commission_fee'] ) ) );
		}
	}
}
