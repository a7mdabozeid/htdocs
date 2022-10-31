<?php
/**
 * The refund request email template.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/common
 */

$products     = get_post_meta( $order_id, 'wps_rma_return_product', true );
$rr_subject   = '';
$rr_reason    = '';
$product_data = '';
// Get pending return request.
if ( isset( $products ) && ! empty( $products ) ) {
	foreach ( $products as $date => $product ) {
		if ( 'pending' === $product['status'] ) {
			$rr_subject = $products[ $date ]['subject'];
			if ( isset( $products[ $date ]['reason'] ) ) {
				$rr_reason = $products[ $date ]['reason'];
			}
			$product_data = $product['products'];
		}
		break;
	}
}
$rr_reason          = ! empty( $rr_reason ) ? $rr_reason : esc_html__( 'No Reason', 'woo-refund-and-exchange-lite' );
$message            =
'<stlye></stlye><div class="wps_rma_refund_req_mail">
	<div class="header">
		<h2>' . $rr_subject . '</h2>
	</div>
	<div class="content">
		<div class="reason">
			<h4>' . esc_html__( 'Reason of Refund', 'woo-refund-and-exchange-lite' ) . '</h4>
			<p>' . $rr_reason . '</p>
		</div>
		<div class="Order">
			<h4>Order #' . $order_id . '</h4>
			<table width="100%" style="border-collapse: collapse;">
				<tbody>
					<tr>
						<th style="border: 1px solid #C7C7C7;">' . esc_html__( 'Product', 'woo-refund-and-exchange-lite' ) . '</th>
						<th style="border: 1px solid #C7C7C7;">' . esc_html__( 'Quantity', 'woo-refund-and-exchange-lite' ) . '</th>
						<th style="border: 1px solid #C7C7C7;">' . esc_html__( 'Price', 'woo-refund-and-exchange-lite' ) . '</th>
					</tr>';
$order_obj          = wc_get_order( $order_id );
$get_order_currency = get_woocommerce_currency_symbol( $order_obj->get_currency() );
$requested_products = $products[ $date ]['products'];
if ( isset( $requested_products ) && ! empty( $requested_products ) ) {
	$total = 0;
	foreach ( $order_obj->get_items() as $item_id => $item ) {
		$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
		foreach ( $requested_products as $requested_product ) {
			if ( isset( $requested_product['item_id'] ) ) {
				if ( $item_id == $requested_product['item_id'] ) {
					if ( isset( $requested_product['variation_id'] ) && $requested_product['variation_id'] > 0 ) {
						$product_obj = wc_get_product( $requested_product['variation_id'] );

					} else {
						$product_obj = wc_get_product( $requested_product['product_id'] );
					}
					$subtotal = $requested_product['price'] * $requested_product['qty'];
					$total   += $subtotal;
					if ( WC()->version < '3.1.0' ) {
						$item_meta      = new WC_Order_Item_Meta( $item, $product_obj );
						$item_meta_html = $item_meta->display( true, true );
					} else {
						$item_meta      = new WC_Order_Item_Product( $item, $product_obj );
						$item_meta_html = wc_display_item_meta( $item_meta, array( 'echo' => false ) );
					}
					$message .= '<tr><td style="border: 1px solid #C7C7C7;">' . $item['name'] . '<br>';
					$message .= '<small>' . $item_meta_html . '</small></td>
								<td style="border: 1px solid #C7C7C7;">' . $requested_product['qty'] . '</td>
								<td style="border: 1px solid #C7C7C7;">' . wps_wrma_format_price( $requested_product['price'] * $requested_product['qty'], $get_order_currency ) . '</td>
								</tr>';
				}
			}
		}
	}
}
$message       .= '<tr>
					<th colspan="2" style="border: 1px solid #C7C7C7;">' . esc_html__( 'Refund Total', 'woo-refund-and-exchange-lite' ) . ':</th>
					<td style="border: 1px solid #C7C7C7;">' . wps_wrma_format_price( $total, $get_order_currency ) . '</td>
				</tr>';
$message        = apply_filters( 'wps_rma_add_shipping_fee_tr', $message );
$message       .= '</tbody>
		</table>
	</div>
	<div class="Customer-detail">
		<h4>' . esc_html__( 'Customer details', 'woo-refund-and-exchange-lite' ) . '</h4>
		<ul>
			<li><p class="info">
				<span class="bold">' . esc_html__( 'Email', 'woo-refund-and-exchange-lite' ) . ': </span>' . $order_obj->get_billing_email() . '
			</p></li>
			<li><p class="info">
				<span class="bold">' . esc_html__( 'Tel', 'woo-refund-and-exchange-lite' ) . ': </span>' . $order_obj->get_billing_phone() . '
			</p></li>
		</ul>
	</div>
	<div class="details">
		<div class="Shipping-detail">
			<h4>' . esc_html__( 'Shipping Address', 'woo-refund-and-exchange-lite' ) . '</h4>
			' . $order_obj->get_formatted_shipping_address() . '
		</div>
		<div class="Billing-detail">
			<h4>' . esc_html__( 'Billing Address', 'woo-refund-and-exchange-lite' ) . '</h4>
			' . $order_obj->get_formatted_billing_address() . '
		</div>
	</div>
</div>';
$attachment     = array();
$to             = get_option( 'woocommerce_email_from_address', get_option( 'admin_email' ) );
$admin_email    = WC()->mailer()->emails['wps_rma_refund_request_email'];
$restrict_mail1 = apply_filters( 'wps_rma_restrict_refund_request_user_mail', true );
$restrict_mail2 = apply_filters( 'wps_rma_restrict_refund_request_admin_mail', true );
if ( $restrict_mail2 ) {
	$admin_email->trigger( $message, $attachment, $to, $order_id );
}
if ( $restrict_mail1 ) {
	$admin_email->trigger( $message, $attachment, $order_obj->get_billing_email(), $order_id );
}
