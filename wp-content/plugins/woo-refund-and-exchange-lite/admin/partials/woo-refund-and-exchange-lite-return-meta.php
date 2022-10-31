<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://wpswings.com/
 * @since 1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_int( $thepostid ) ) {
	$thepostid = $post->ID;
}
if ( ! is_object( $theorder ) ) {
	$theorder = wc_get_order( $thepostid );
}
$order_obj    = $theorder;
$order_id     = $order_obj->get_id();
$return_datas = get_post_meta( $order_id, 'wps_rma_return_product', true );
$item_type    =
// Order Item Type.
apply_filters( 'woocommerce_admin_order_item_types', 'line_item' );
$line_items         = $order_obj->get_items( $item_type );
$get_order_currency = get_woocommerce_currency_symbol( $order_obj->get_currency() );
if ( isset( $return_datas ) && ! empty( $return_datas ) ) {
	$ref_meth = get_option( $order_id . 'wps_rma_refund_method' );
	foreach ( $return_datas as $key => $return_data ) {
		$date          = date_i18n( wc_date_format(), $key );
		$refund_method = isset( $ref_meth ) ? $ref_meth : '';
		$refund_method = isset( $return_data['refund_method'] ) ? $return_data['refund_method'] : $refund_method;
		?>
		<p><?php esc_html_e( 'Following product refund request made on', 'woo-refund-and-exchange-lite' ); ?> <b><?php echo esc_html( $date ); ?>.</b></p>
		<div id="wps_rma_return_meta_wrapper">
			<table>
				<thead>
				<tr>
					<th><?php esc_html_e( 'Item', 'woo-refund-and-exchange-lite' ); ?></th>
					<th><?php esc_html_e( 'Name', 'woo-refund-and-exchange-lite' ); ?></th>
					<th><?php esc_html_e( 'Price', 'woo-refund-and-exchange-lite' ); ?></th>
					<th><?php esc_html_e( 'Quantity', 'woo-refund-and-exchange-lite' ); ?></th>
					<th><?php esc_html_e( 'Total', 'woo-refund-and-exchange-lite' ); ?></th>
				</tr>
				</thead>
				<tbody>
					<?php
					$total             = 0;
					$reduced_total     = 0;
					$pro_id            = array();
					$price_reduce_flag = false;
					$total_refund_amu  = 0;
					$return_products   = $return_data['products'];
					foreach ( $line_items as $item_id => $item ) {
						foreach ( $return_products as $returnkey => $return_product ) {
							if ( $return_product['item_id'] == $item_id ) {
								if ( $item->get_variation_id() ) {
									$product_id = $item->get_variation_id();
								} else {
									$product_id = $item->get_product_id();
								}
								$product = wc_get_product( $product_id );
								$prod_price      = $return_product['price'];
								$total_pro_price = $prod_price * $return_product['qty'];
								?>
								<tr>
									<td class="thumb">
									<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $product->get_image() ) . '</div>'; ?>
									</td>
									<td>
										<?php
										echo esc_html( $product->get_name() );
										if ( ! empty( $product->get_sku() ) ) {
											echo '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'woo-refund-and-exchange-lite' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>';
										}
										if ( $item->get_variation_id() ) {
											echo '<div class="wc-order-item-variation"><strong>' . esc_html__( 'Variation ID:', 'woo-refund-and-exchange-lite' ) . '</strong> ';
											echo esc_html( $item->get_variation_id() );
											echo '</div>';
										}
										$item_meta = new WC_Order_Item_Product( $item );
										wc_display_item_meta( $item_meta );
										?>
										<td><?php echo wp_kses_post( wps_wrma_format_price( $prod_price, $get_order_currency ) ); ?></td>
										<td><?php echo esc_html( $return_product['qty'] ); ?></td>
										<td><?php echo wp_kses_post( wps_wrma_format_price( $total_pro_price, $get_order_currency ) ); ?></td>
									</td>
								</tr>
								<?php
								$total += $total_pro_price;
							}
						}
					}
					$total_refund_amu =
					// Change refund total amount on product meta.
					apply_filters( 'wps_rma_refund_total_amount', $total, $order_id );
					?>
					<tr>
						<th colspan="4"><?php esc_html_e( 'Total', 'woo-refund-and-exchange-lite' ); ?></th>
						<th><?php echo wp_kses_post( wps_wrma_format_price( $total, $get_order_currency ) ); ?></th>
					</tr>
				</tbody>
			</table>
		</div>
		<div>
		<?php
		// Add Global Fee.
		do_action( 'wps_rma_global_shipping_fee', $order_id );
		?>
		</div>
		<div class="wps_rma_extra_reason">
			<p>
				<strong><?php esc_html_e( 'Refund Amount', 'woo-refund-and-exchange-lite' ); ?> : </strong> <?php echo wp_kses_post( wps_wrma_format_price( $total_refund_amu, $get_order_currency ) ); ?>
			</p>
		</div>
		<div class="wps_rma_reason">
			<p><strong><?php esc_html_e( 'Subject', 'woo-refund-and-exchange-lite' ); ?> : </strong><i> <?php echo esc_html( $return_data['subject'] ); ?></i></p>
			<?php
			if ( isset( $return_data['reason'] ) && ! empty( $return_data['reason'] ) ) {
				echo '<p><b>' . esc_html( 'Reason', 'woo-refund-and-exchange-lite' ) . ': </b></p>';
				echo '<p>' . esc_html( $return_data['reason'] ) . '</p>';
			}
			?>
			<?php
			$bank_details = get_post_meta( $order_id, 'wps_rma_bank_details', true );
			if ( ! empty( $bank_details ) ) {
				?>
				<p><strong><?php esc_html_e( 'Bank Details', 'woo-refund-and-exchange-lite' ); ?> :</strong><i> <?php echo esc_html( $bank_details ); ?></i></p>
				<?php
			}
			?>
			<?php
			$req_attachments = get_post_meta( $order_id, 'wps_rma_return_attachment', true );
			if ( isset( $req_attachments ) && ! empty( $req_attachments ) ) {
				?>
				<p><b><?php esc_html_e( 'Attachment', 'woo-refund-and-exchange-lite' ); ?> :</b></p>
				<?php
				if ( is_array( $req_attachments ) ) {
					foreach ( $req_attachments as $da => $attachments ) {
						$count = 1;
						foreach ( $attachments['files'] as $attachment ) {
							if ( $attachment !== $order_id . '-' ) {
								?>
								<a href="<?php echo esc_html( content_url() . '/attachment/' ); ?><?php echo esc_html( $attachment ); ?>" target="_blank"><?php esc_html_e( 'Attachment', 'woo-refund-and-exchange-lite' ); ?>-<?php echo esc_html( $count ); ?></a>
								<?php
								$count++;
							} else {
								?>
									<p><?php esc_html_e( 'No attachment from customer', 'woo-refund-and-exchange-lite' ); ?></p>
								<?php
							}
						}
						break;
					}
				}
			}

			// Show some fields in the refund request metabox.
			do_action( 'wps_rma_show_extra_field', $order_id );
			?>
			<input type="hidden" name="wps_rma_total_amount_for_refund" class="wps_rma_total_amount_for_refund" value="<?php echo esc_html( $total_refund_amu ); ?>">
			<input type="hidden" value="<?php echo esc_html( $return_data['subject'] ); ?>" id="wps_rma_refund_reason">
			<?php
			if ( 'pending' === $return_data['status'] ) {
				// To show some fields when refund request is pending.
				do_action( 'wps_rma_return_ship_attach_upload_html', $order_id );
				?>
				<p id="wps_rma_return_package">
				<input type="button" value="<?php esc_html_e( 'Accept Request', 'woo-refund-and-exchange-lite' ); ?>" class="button button-primary" id="wps_rma_accept_return" data-orderid="<?php echo esc_html( $order_id ); ?>" data-date="<?php echo esc_html( $key ); ?>">
				<input type="button" value="<?php esc_html_e( 'Cancel Request', 'woo-refund-and-exchange-lite' ); ?>" class="button button-primary" id="wps_rma_cancel_return" data-orderid="<?php echo esc_html( $order_id ); ?>" data-date="<?php echo esc_html( $key ); ?>">
				</p>
				<?php
			}
			?>
		</div>
		<div class="wps_rma_return_loader">
			<img src="<?php echo esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'admin/image/loader.gif' ); ?>">
		</div>
		<?php
		if ( 'complete' === $return_data['status'] ) {
			?>
			<input type="hidden" name="wps_rma_total_amount_for_refund" class="wps_rma_total_amount_for_refund" value="<?php echo esc_html( $total_refund_amu ); ?>">
			<input type="hidden" value="<?php echo esc_html( $return_data['subject'] ); ?>" id="wps_rma_refund_reason">
			<?php
			$approve_date          = date_i18n( wc_date_format(), $return_data['approve_date'] );
			$wps_rma_refund_amount = get_post_meta( $order_id, 'wps_rma_left_amount_done', true );
			esc_html_e( 'Following product refund request is approved on', 'woo-refund-and-exchange-lite' );
			?>
			<b>
				<?php echo esc_html( $approve_date ); ?>.
			</b>
			<?php
			if ( 'yes' !== $wps_rma_refund_amount ) {
				?>
				<input type="button" class="button button-primary" name="wps_rma_left_amount" data-refund_method="<?php echo esc_html( $refund_method ); ?>" class="button button-primary" data-orderid="<?php echo esc_html( $order_id ); ?>" id="wps_rma_left_amount" Value="<?php esc_html_e( 'Refund Amount', 'woo-refund-and-exchange-lite' ); ?>" >
				<?php
			}
			$manage_stock = get_option( 'wps_rma_refund_manage_stock', 'no' );
			// to show manage stock button when refund request is approved.
			$wps_rma_manage_stock_for_return = get_post_meta( $order_id, 'wps_rma_manage_stock_for_return', true );
			if ( '' === $wps_rma_manage_stock_for_return ) {
				$wps_rma_manage_stock_for_return = 'yes';
			}
			if ( 'on' === $manage_stock && 'yes' === $wps_rma_manage_stock_for_return ) {
				?>
				<div id="wps_rma_stock_button_wrapper"><?php esc_html_e( 'When Product Back in stock then for stock management click on ', 'woo-refund-and-exchange-lite' ); ?> <input type="button" class="button button-primary" name="wps_rma_stock_back" class="button button-primary" id="wps_rma_stock_back" data-type="wps_rma_return" data-orderid="<?php echo esc_html( $order_id ); ?>" Value="<?php esc_html_e( 'Manage Stock', 'woo-refund-and-exchange-lite' ); ?>" ></div> 
				<?php
			}
		}

		if ( 'cancel' === $return_data['status'] ) {
			$cancel_date = date_i18n( wc_date_format(), $return_data['cancel_date'] );
			esc_html_e( 'Following product refund request is cancelled on', 'woo-refund-and-exchange-lite' );
			?>
			<b><?php echo esc_html( $cancel_date ); ?>.</b>
			<?php
		}
		?>
		<?php
	}
} else {
	$initiate_refund_request =
	// Initiate return bool to show button.
	apply_filters( 'wps_rma_initiate_refund_request', false );
	if ( ! $initiate_refund_request ) {
		$wps_rma_return_request_form_page_id = get_option( 'wps_rma_return_request_form_page_id', true );
		$page_id                             = $wps_rma_return_request_form_page_id;
		$return_url                          = get_permalink( $page_id );
		$return_url                          = add_query_arg( 'order_id', $order_obj->get_id(), $return_url );
		$return_url                          = wp_nonce_url( $return_url, 'wps_rma_nonce', 'wps_rma_nonce' );
		?>
		<p><?php esc_html_e( 'No request from customer', 'woo-refund-and-exchange-lite' ); ?></p>
		<a target="_blank" class="button button-primary" href="<?php echo esc_html( $return_url ); ?>"><b><?php esc_html_e( 'Initiate Refund Request', 'woo-refund-and-exchange-lite' ); ?></b></a>
		<?php
	}
}
?>
<hr/>
