<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MPS_Calculator {
	public function calculate_order( WC_Order $order, array $settings ) {
		$gross_sales       = (float) $order->get_subtotal();
		$shipping_collected = (float) $order->get_shipping_total();
		$discounts         = (float) $order->get_discount_total();
		$tax_total         = (float) $order->get_total_tax();

		$payment_fee = (float) $order->get_meta( '_mps_payment_fee', true );
		if ( 0 === $payment_fee && ! empty( $settings['default_payment_fee_rate'] ) ) {
			$payment_fee = ( $gross_sales + $shipping_collected - $discounts ) * ( (float) $settings['default_payment_fee_rate'] / 100 );
		}

		$gross_incl_tax = ( $gross_sales + $shipping_collected + $tax_total ) - $discounts;
		$net_excl_tax   = ( $gross_sales + $shipping_collected ) - $discounts;

		$product_costs = 0.0;
		$sku_missing   = false;

		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) {
				continue;
			}

			$product  = $item->get_product();
			$quantity = (float) $item->get_quantity();

			if ( ! $product ) {
				continue;
			}

			$sku = $product->get_sku();
			if ( '' === $sku ) {
				$sku_missing = true;
			}

			$purchase_cost  = (float) $product->get_meta( '_mps_purchase_cost', true );
			$packaging_cost = (float) $product->get_meta( '_mps_packaging_cost', true );
			$fixed_overhead = (float) $product->get_meta( '_mps_fixed_overhead', true );
			$labor_cost     = (float) $product->get_meta( '_mps_labor_cost', true );
			$waste_rate     = (float) $product->get_meta( '_mps_waste_rate', true );
			$procurement    = (float) $product->get_meta( '_mps_procurement_cost', true );

			$unit_cost = $purchase_cost + $packaging_cost + $fixed_overhead + $labor_cost + $procurement;
			if ( $waste_rate > 0 ) {
				$unit_cost = $unit_cost * ( 1 + ( $waste_rate / 100 ) );
			}
			$product_costs += $unit_cost * $quantity;
		}

		$shipping_cost = (float) $order->get_meta( '_mps_shipping_cost', true );
		$shipping_method = isset( $settings['shipping_cost_method'] ) ? $settings['shipping_cost_method'] : 'manual';
		if ( 0 === $shipping_cost && 'fixed' === $shipping_method && ! empty( $settings['default_shipping_cost'] ) ) {
			$shipping_cost = (float) $settings['default_shipping_cost'];
		}

		$commission_fee = $this->get_fee_value( $order, '_mps_fee_commission', '_mps_commission_fee' );
		$service_fee    = $this->get_fee_value( $order, '_mps_fee_service', '' );
		$commission_vat = $this->get_fee_value( $order, '_mps_fee_commission_vat', '' );
		$shipping_deduction = $this->get_fee_value( $order, '_mps_fee_shipping_deduction', '' );
		$coupon_cost    = $this->get_fee_value( $order, '_mps_fee_coupon', '' );
		$other_fee      = $this->get_fee_value( $order, '_mps_fee_other', '' );
		$promo_cost     = (float) $order->get_meta( '_mps_promo_cost', true );

		$total_fees = $commission_fee + $service_fee + $commission_vat + $shipping_deduction + $coupon_cost + $other_fee + $payment_fee + $promo_cost;
		$cogs       = $product_costs + $shipping_cost;
		$net_revenue = $net_excl_tax - $total_fees;
		$net_profit = $net_excl_tax - $total_fees - $cogs;
		$margin     = 0.0;
		if ( $net_excl_tax > 0 ) {
			$margin = ( $net_profit / $net_excl_tax ) * 100;
		}

		return array(
			'gross_sales'        => $gross_sales,
			'shipping_collected' => $shipping_collected,
			'discounts'          => $discounts,
			'tax_total'          => $tax_total,
			'gross_incl_tax'     => $gross_incl_tax,
			'net_excl_tax'       => $net_excl_tax,
			'payment_fee'        => $payment_fee,
			'net_revenue'        => $net_revenue,
			'product_costs'      => $product_costs,
			'shipping_cost'      => $shipping_cost,
			'commission_fee'     => $commission_fee,
			'service_fee'        => $service_fee,
			'commission_vat'     => $commission_vat,
			'shipping_deduction' => $shipping_deduction,
			'coupon_cost'        => $coupon_cost,
			'other_fee'          => $other_fee,
			'promo_cost'         => $promo_cost,
			'total_fees'         => $total_fees,
			'cogs'               => $cogs,
			'net_profit'         => $net_profit,
			'margin'             => $margin,
			'sku_missing'        => $sku_missing,
		);
	}

	private function get_fee_value( WC_Order $order, $primary_key, $fallback_key ) {
		$value = (float) $order->get_meta( $primary_key, true );
		if ( 0 === $value && $fallback_key ) {
			$value = (float) $order->get_meta( $fallback_key, true );
		}

		return $value;
	}

	public function calculate_product_summary( WC_Order $order, array &$product_rows ) {
		foreach ( $order->get_items() as $item ) {
			if ( ! $item instanceof WC_Order_Item_Product ) {
				continue;
			}

			$product  = $item->get_product();
			$quantity = (float) $item->get_quantity();

			if ( ! $product ) {
				continue;
			}

			$product_id = $product->get_id();
			$sku        = $product->get_sku();
			$key        = $sku ? $sku : 'product-' . $product_id;

			if ( ! isset( $product_rows[ $key ] ) ) {
				$product_rows[ $key ] = array(
					'product_id'  => $product_id,
					'sku'         => $sku,
					'name'        => $product->get_name(),
					'quantity'    => 0,
					'net_revenue' => 0,
					'total_cost'  => 0,
					'net_profit'  => 0,
					'sku_missing' => ( '' === $sku ),
				);
			}

			$product_rows[ $key ]['quantity'] += $quantity;
		}
	}
}
