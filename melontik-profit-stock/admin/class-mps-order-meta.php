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
		$commission    = $order->get_meta( '_mps_fee_commission', true );
		if ( '' === $commission ) {
			$commission = $order->get_meta( '_mps_commission_fee', true );
		}
		$service_fee     = $order->get_meta( '_mps_fee_service', true );
		$commission_vat  = $order->get_meta( '_mps_fee_commission_vat', true );
		$shipping_deduct = $order->get_meta( '_mps_fee_shipping_deduction', true );
		$coupon_cost     = $order->get_meta( '_mps_fee_coupon', true );
		$other_fee       = $order->get_meta( '_mps_fee_other', true );
		$promo_cost      = $order->get_meta( '_mps_promo_cost', true );
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
		<p>
			<label for="mps_service_fee"><strong><?php esc_html_e( 'Hizmet Bedeli (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_service_fee" id="mps_service_fee" value="<?php echo esc_attr( $service_fee ); ?>" />
		</p>
		<p>
			<label for="mps_commission_vat"><strong><?php esc_html_e( 'Komisyon KDV (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_commission_vat" id="mps_commission_vat" value="<?php echo esc_attr( $commission_vat ); ?>" />
		</p>
		<p>
			<label for="mps_shipping_deduction"><strong><?php esc_html_e( 'Kargo Kesintisi (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_shipping_deduction" id="mps_shipping_deduction" value="<?php echo esc_attr( $shipping_deduct ); ?>" />
		</p>
		<p>
			<label for="mps_coupon_cost"><strong><?php esc_html_e( 'Kupon/Kampanya (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_coupon_cost" id="mps_coupon_cost" value="<?php echo esc_attr( $coupon_cost ); ?>" />
		</p>
		<p>
			<label for="mps_other_fee"><strong><?php esc_html_e( 'Diğer Kesinti (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_other_fee" id="mps_other_fee" value="<?php echo esc_attr( $other_fee ); ?>" />
		</p>
		<p>
			<label for="mps_promo_cost"><strong><?php esc_html_e( 'Promosyon Maliyeti (TL)', 'melontik-profit-stock' ); ?></strong></label>
			<input type="number" step="0.01" name="mps_promo_cost" id="mps_promo_cost" value="<?php echo esc_attr( $promo_cost ); ?>" />
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
			update_post_meta( $post_id, '_mps_fee_commission', wc_format_decimal( wp_unslash( $_POST['mps_commission_fee'] ) ) );
		}

		if ( isset( $_POST['mps_service_fee'] ) ) {
			update_post_meta( $post_id, '_mps_fee_service', wc_format_decimal( wp_unslash( $_POST['mps_service_fee'] ) ) );
		}

		if ( isset( $_POST['mps_commission_vat'] ) ) {
			update_post_meta( $post_id, '_mps_fee_commission_vat', wc_format_decimal( wp_unslash( $_POST['mps_commission_vat'] ) ) );
		}

		if ( isset( $_POST['mps_shipping_deduction'] ) ) {
			update_post_meta( $post_id, '_mps_fee_shipping_deduction', wc_format_decimal( wp_unslash( $_POST['mps_shipping_deduction'] ) ) );
		}

		if ( isset( $_POST['mps_coupon_cost'] ) ) {
			update_post_meta( $post_id, '_mps_fee_coupon', wc_format_decimal( wp_unslash( $_POST['mps_coupon_cost'] ) ) );
		}

		if ( isset( $_POST['mps_other_fee'] ) ) {
			update_post_meta( $post_id, '_mps_fee_other', wc_format_decimal( wp_unslash( $_POST['mps_other_fee'] ) ) );
		}

		if ( isset( $_POST['mps_promo_cost'] ) ) {
			update_post_meta( $post_id, '_mps_promo_cost', wc_format_decimal( wp_unslash( $_POST['mps_promo_cost'] ) ) );
		}
	}
}
