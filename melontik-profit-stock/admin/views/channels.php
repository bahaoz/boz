<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$channel_settings = get_option( 'mps_channel_settings', array() );
$channels = array(
	'woo'         => __( 'WooCommerce', 'melontik-profit-stock' ),
	'trendyol'    => __( 'Trendyol', 'melontik-profit-stock' ),
	'hepsiburada' => __( 'Hepsiburada', 'melontik-profit-stock' ),
);

$default_values = array(
	'commission_rate'      => 0,
	'service_fee_rate'     => 0,
	'commission_vat_rate'  => 0,
	'shipping_deduction_rate' => 0,
	'payment_fee_rate'     => 0,
	'coupon_support_rate'  => 0,
	'other_fee_rate'       => 0,
	'commission_vat_included' => 0,
);
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Kanallar', 'melontik-profit-stock' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'mps_channel_settings_group' ); ?>
		<div style="display:grid; gap:20px;">
			<?php foreach ( $channels as $channel_key => $channel_label ) : ?>
				<?php
				$values = isset( $channel_settings[ $channel_key ] ) ? array_merge( $default_values, $channel_settings[ $channel_key ] ) : $default_values;
				?>
				<div class="card">
					<h2><?php echo esc_html( $channel_label ); ?></h2>
					<table class="form-table">
						<tr>
							<th><?php esc_html_e( 'Komisyon Oranı (%)', 'melontik-profit-stock' ); ?></th>
							<td><input type="number" step="0.01" name="mps_channel_settings[<?php echo esc_attr( $channel_key ); ?>][commission_rate]" value="<?php echo esc_attr( $values['commission_rate'] ); ?>" /></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Hizmet Bedeli (%)', 'melontik-profit-stock' ); ?></th>
							<td><input type="number" step="0.01" name="mps_channel_settings[<?php echo esc_attr( $channel_key ); ?>][service_fee_rate]" value="<?php echo esc_attr( $values['service_fee_rate'] ); ?>" /></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Komisyon KDV Oranı (%)', 'melontik-profit-stock' ); ?></th>
							<td><input type="number" step="0.01" name="mps_channel_settings[<?php echo esc_attr( $channel_key ); ?>][commission_vat_rate]" value="<?php echo esc_attr( $values['commission_vat_rate'] ); ?>" /></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Komisyon KDV Dahil', 'melontik-profit-stock' ); ?></th>
							<td><label><input type="checkbox" name="mps_channel_settings[<?php echo esc_attr( $channel_key ); ?>][commission_vat_included]" value="1" <?php checked( $values['commission_vat_included'], 1 ); ?> /> <?php esc_html_e( 'Dahil', 'melontik-profit-stock' ); ?></label></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Kargo Kesintisi (%)', 'melontik-profit-stock' ); ?></th>
							<td><input type="number" step="0.01" name="mps_channel_settings[<?php echo esc_attr( $channel_key ); ?>][shipping_deduction_rate]" value="<?php echo esc_attr( $values['shipping_deduction_rate'] ); ?>" /></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Ödeme Kesintisi (%)', 'melontik-profit-stock' ); ?></th>
							<td><input type="number" step="0.01" name="mps_channel_settings[<?php echo esc_attr( $channel_key ); ?>][payment_fee_rate]" value="<?php echo esc_attr( $values['payment_fee_rate'] ); ?>" /></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Kupon/Kampanya Katkısı (%)', 'melontik-profit-stock' ); ?></th>
							<td><input type="number" step="0.01" name="mps_channel_settings[<?php echo esc_attr( $channel_key ); ?>][coupon_support_rate]" value="<?php echo esc_attr( $values['coupon_support_rate'] ); ?>" /></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Diğer Kesinti (%)', 'melontik-profit-stock' ); ?></th>
							<td><input type="number" step="0.01" name="mps_channel_settings[<?php echo esc_attr( $channel_key ); ?>][other_fee_rate]" value="<?php echo esc_attr( $values['other_fee_rate'] ); ?>" /></td>
						</tr>
					</table>
				</div>
			<?php endforeach; ?>
		</div>
		<?php submit_button(); ?>
	</form>
</div>
