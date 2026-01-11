<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = get_option( 'mps_settings', array() );

$start_date = '';
$end_date   = '';
$channel    = 'woo';

if ( isset( $_GET['mps_filter_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['mps_filter_nonce'] ) ), 'mps_filter' ) ) {
	$start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
	$end_date   = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';
	$channel    = isset( $_GET['channel'] ) ? sanitize_text_field( wp_unslash( $_GET['channel'] ) ) : 'woo';
}

$order_repo = new MPS_Order_Repository();
$calculator = new MPS_Calculator();

$orders = $order_repo->get_orders( $start_date, $end_date, $channel );

$order_rows   = array();
$product_rows = array();

$total_net_revenue = 0;
$total_cost        = 0;
$total_profit      = 0;

foreach ( $orders as $order ) {
	if ( ! $order instanceof WC_Order ) {
		continue;
	}

	$calc = $calculator->calculate_order( $order, $settings );

	$total_net_revenue += $calc['net_revenue'];
	$total_cost        += $calc['total_cost'];
	$total_profit      += $calc['net_profit'];

	$order_rows[] = array(
		'id'          => $order->get_id(),
		'number'      => $order->get_order_number(),
		'date'        => $order->get_date_created() ? $order->get_date_created()->date_i18n( 'Y-m-d' ) : '',
		'channel'     => $order->get_meta( '_mps_channel', true ) ? $order->get_meta( '_mps_channel', true ) : 'woo',
		'net_revenue' => $calc['net_revenue'],
		'total_cost'  => $calc['total_cost'],
		'net_profit'  => $calc['net_profit'],
		'margin'      => $calc['margin'],
		'sku_missing' => $calc['sku_missing'],
	);

	foreach ( $order->get_items() as $item ) {
		if ( ! $item instanceof WC_Order_Item_Product ) {
			continue;
		}

		$product = $item->get_product();
		if ( ! $product ) {
			continue;
		}

		$sku   = $product->get_sku();
		$key   = $sku ? $sku : 'product-' . $product->get_id();
		$qty   = (float) $item->get_quantity();
		$share = $order->get_subtotal() > 0 ? ( (float) $item->get_subtotal() / (float) $order->get_subtotal() ) : 0;

		if ( ! isset( $product_rows[ $key ] ) ) {
			$product_rows[ $key ] = array(
				'product_id'  => $product->get_id(),
				'sku'         => $sku,
				'name'        => $product->get_name(),
				'quantity'    => 0,
				'net_revenue' => 0,
				'total_cost'  => 0,
				'net_profit'  => 0,
				'sku_missing' => ( '' === $sku ),
			);
		}

		$product_rows[ $key ]['quantity']    += $qty;
		$product_rows[ $key ]['net_revenue'] += $calc['net_revenue'] * $share;
		$product_rows[ $key ]['total_cost']  += $calc['total_cost'] * $share;
		$product_rows[ $key ]['net_profit']  += $calc['net_profit'] * $share;
	}
}

$average_margin = 0;
if ( $total_net_revenue > 0 ) {
	$average_margin = ( $total_profit / $total_net_revenue ) * 100;
}

$per_page   = 20;
$order_page = isset( $_GET['order_page'] ) ? max( 1, (int) $_GET['order_page'] ) : 1;
$product_page = isset( $_GET['product_page'] ) ? max( 1, (int) $_GET['product_page'] ) : 1;

$order_total_pages   = (int) ceil( count( $order_rows ) / $per_page );
$product_total_pages = (int) ceil( count( $product_rows ) / $per_page );

$order_rows_paged   = array_slice( $order_rows, ( $order_page - 1 ) * $per_page, $per_page );
$product_rows_paged = array_slice( array_values( $product_rows ), ( $product_page - 1 ) * $per_page, $per_page );

?>
<div class="wrap">
	<h1><?php esc_html_e( 'Karlılık Raporları', 'melontik-profit-stock' ); ?></h1>

	<form method="get">
		<input type="hidden" name="page" value="mps-profit" />
		<?php wp_nonce_field( 'mps_filter', 'mps_filter_nonce' ); ?>
		<table class="form-table">
			<tr>
				<th><label for="start_date"><?php esc_html_e( 'Başlangıç', 'melontik-profit-stock' ); ?></label></th>
				<td><input type="date" id="start_date" name="start_date" value="<?php echo esc_attr( $start_date ); ?>" /></td>
				<th><label for="end_date"><?php esc_html_e( 'Bitiş', 'melontik-profit-stock' ); ?></label></th>
				<td><input type="date" id="end_date" name="end_date" value="<?php echo esc_attr( $end_date ); ?>" /></td>
				<th><label for="channel"><?php esc_html_e( 'Kanal', 'melontik-profit-stock' ); ?></label></th>
				<td>
					<select id="channel" name="channel">
						<option value="woo" <?php selected( $channel, 'woo' ); ?>>WooCommerce</option>
						<option value="trendyol" <?php selected( $channel, 'trendyol' ); ?>>Trendyol</option>
						<option value="hepsiburada" <?php selected( $channel, 'hepsiburada' ); ?>>Hepsiburada</option>
					</select>
				</td>
			</tr>
		</table>
		<?php submit_button( __( 'Filtrele', 'melontik-profit-stock' ) ); ?>
	</form>

	<h2><?php esc_html_e( 'Özet KPI', 'melontik-profit-stock' ); ?></h2>
	<div style="display:flex; gap:20px; flex-wrap:wrap;">
		<div class="card">
			<h3><?php esc_html_e( 'Toplam Net Gelir', 'melontik-profit-stock' ); ?></h3>
			<p><?php echo wp_kses_post( wc_price( $total_net_revenue ) ); ?></p>
		</div>
		<div class="card">
			<h3><?php esc_html_e( 'Toplam Maliyet', 'melontik-profit-stock' ); ?></h3>
			<p><?php echo wp_kses_post( wc_price( $total_cost ) ); ?></p>
		</div>
		<div class="card">
			<h3><?php esc_html_e( 'Toplam Net Kâr', 'melontik-profit-stock' ); ?></h3>
			<p><?php echo wp_kses_post( wc_price( $total_profit ) ); ?></p>
		</div>
		<div class="card">
			<h3><?php esc_html_e( 'Ortalama Marj', 'melontik-profit-stock' ); ?></h3>
			<p><?php echo esc_html( wc_format_decimal( $average_margin, 2 ) ); ?>%</p>
		</div>
	</div>

	<h2><?php esc_html_e( 'Sipariş Bazında', 'melontik-profit-stock' ); ?></h2>
	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Sipariş No', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Tarih', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Kanal', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Net Gelir', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Maliyet', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Net Kâr', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Marj', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'SKU Uyarısı', 'melontik-profit-stock' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $order_rows_paged ) ) : ?>
				<tr><td colspan="8"><?php esc_html_e( 'Kayıt bulunamadı.', 'melontik-profit-stock' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $order_rows_paged as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row['number'] ); ?></td>
						<td><?php echo esc_html( $row['date'] ); ?></td>
						<td><?php echo esc_html( strtoupper( $row['channel'] ) ); ?></td>
						<td><?php echo wp_kses_post( wc_price( $row['net_revenue'] ) ); ?></td>
						<td><?php echo wp_kses_post( wc_price( $row['total_cost'] ) ); ?></td>
						<td><?php echo wp_kses_post( wc_price( $row['net_profit'] ) ); ?></td>
						<td><?php echo esc_html( wc_format_decimal( $row['margin'], 2 ) ); ?>%</td>
						<td><?php echo $row['sku_missing'] ? esc_html__( 'Eksik', 'melontik-profit-stock' ) : esc_html__( 'Tam', 'melontik-profit-stock' ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<?php if ( $order_total_pages > 1 ) : ?>
		<p>
			<?php for ( $page = 1; $page <= $order_total_pages; $page++ ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'order_page' => $page ) ) ); ?>">
					<?php echo esc_html( $page ); ?>
				</a>
			<?php endfor; ?>
		</p>
	<?php endif; ?>

	<h2><?php esc_html_e( 'Ürün Bazında', 'melontik-profit-stock' ); ?></h2>
	<table class="widefat striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'SKU', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Ürün', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Satış Adedi', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Net Gelir', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Maliyet', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Net Kâr', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'Marj', 'melontik-profit-stock' ); ?></th>
				<th><?php esc_html_e( 'SKU Uyarısı', 'melontik-profit-stock' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $product_rows_paged ) ) : ?>
				<tr><td colspan="8"><?php esc_html_e( 'Kayıt bulunamadı.', 'melontik-profit-stock' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $product_rows_paged as $row ) : ?>
					<?php
					$margin = 0;
					if ( $row['net_revenue'] > 0 ) {
						$margin = ( $row['net_profit'] / $row['net_revenue'] ) * 100;
					}
					?>
					<tr>
						<td><?php echo esc_html( $row['sku'] ? $row['sku'] : '-' ); ?></td>
						<td><?php echo esc_html( $row['name'] ); ?></td>
						<td><?php echo esc_html( $row['quantity'] ); ?></td>
						<td><?php echo wp_kses_post( wc_price( $row['net_revenue'] ) ); ?></td>
						<td><?php echo wp_kses_post( wc_price( $row['total_cost'] ) ); ?></td>
						<td><?php echo wp_kses_post( wc_price( $row['net_profit'] ) ); ?></td>
						<td><?php echo esc_html( wc_format_decimal( $margin, 2 ) ); ?>%</td>
						<td><?php echo $row['sku_missing'] ? esc_html__( 'Eksik', 'melontik-profit-stock' ) : esc_html__( 'Tam', 'melontik-profit-stock' ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<?php if ( $product_total_pages > 1 ) : ?>
		<p>
			<?php for ( $page = 1; $page <= $product_total_pages; $page++ ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array( 'product_page' => $page ) ) ); ?>">
					<?php echo esc_html( $page ); ?>
				</a>
			<?php endfor; ?>
		</p>
	<?php endif; ?>
</div>
