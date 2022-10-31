<?php
/**
 * The public-facing functionality of the plugin for return request form.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $_GET['wps_rma_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['wps_rma_nonce'] ) ), 'wps_rma_nonce' ) && isset( $_GET['order_id'] ) && current_user_can( 'wps-rma-refund-request' ) ) {
	$order_id = sanitize_text_field( wp_unslash( $_GET['order_id'] ) );
} else {
	$order_id = '';
}
$allowed = 'yes';
if ( ! empty( $order_id ) ) {
	$order_obj = wc_get_order( $order_id );
	if ( ! empty( $order_obj ) ) {
		$condition = wps_rma_show_buttons( 'refund', $order_obj );
	} else {
		$condition = esc_html__( 'Please give the correct order id', 'woo-refund-and-exchange-lite' );
	}
}
$rr_subject = '';
$rr_reason  = '';
if ( isset( $condition ) && 'yes' === $condition && isset( $order_id ) && ! empty( $order_id ) && ! empty( $order_obj ) ) {
	$order_customer_id = get_post_meta( $order_id, '_customer_user', true );
	$user              = wp_get_current_user();
	$allowed_roles     = array( 'editor', 'administrator', 'author' );
	if ( ! array_intersect( $allowed_roles, $user->roles ) ) {
		if ( get_current_user_id() > 0 && get_current_user_id() != $order_customer_id ) {
			$myaccount_page     = get_option( 'woocommerce_myaccount_page_id' );
			$myaccount_page_url = get_permalink( $myaccount_page );
			$condition          = wp_kses_post( "This order #$order_id is not associated to your account. <a href='$myaccount_page_url'>Click Here</a>" );
		}
	}
}
$products = get_post_meta( $order_id, 'wps_rma_return_product', true );
// Get pending return request.
if ( isset( $products ) && ! empty( $products ) ) {
	foreach ( $products as $date => $product ) {
		if ( 'pending' === $product['status'] ) {
			$rr_subject = $products[ $date ]['subject'];
			if ( isset( $products[ $date ]['reason'] ) ) {
				$rr_reason = $products[ $date ]['reason'];
			}
			$product_data = $product['products'];
			$allowed      = 'yes';
		}
		break;
	}
}
get_header( 'shop' );


$wps_wrma_show_sidebar_on_form =
// Side show/hide on refund request form.
apply_filters( 'wps_rma_refund_form_sidebar', true );
if ( $wps_wrma_show_sidebar_on_form ) {
	// Before Main Content.
	do_action( 'woocommerce_before_main_content' );
}
if ( isset( $condition ) && 'yes' === $condition ) {
	$wps_refund_wrapper_class = get_option( 'wps_wrma_refund_form_wrapper_class' );
	$wps_return_css           = get_option( 'wps_rma_refund_form_css' );
	?>
	<style><?php echo wp_kses_post( $wps_return_css ); ?></style>
	<div class="wps_rma_refund_form_wrapper wps-rma-form__wrapper <?php echo esc_html( $wps_refund_wrapper_class ); ?>">
		<div id="wps_rma_return_request_container" class="wps-rma-form__header">
			<h1 class="wps-rma-form__heading"><?php esc_html_e( 'Order\'s Product Refund Request Form', 'woo-refund-and-exchange-lite' ); ?></h1>
		</div>
		<ul id="wps_rma_return_alert" ></ul>
		<div class="wps_rma_product_table_wrapper wps-rma-product__table-wrapper">
			<table class="wps-rma-product__table">
				<thead >
					<tr>
						<?php
						// Add extra field in the thead of the table.
						do_action( 'wps_rma_add_extra_column_refund_form', $order_id );
						?>
						<th><?php esc_html_e( 'Product', 'woo-refund-and-exchange-lite' ); ?></th>
						<th><?php esc_html_e( 'Quantity', 'woo-refund-and-exchange-lite' ); ?></th>
						<th><?php esc_html_e( 'Total', 'woo-refund-and-exchange-lite' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$wps_total_actual_price = 0;
					$wps_rma_check_tax      = get_option( $order_id . 'check_tax', false );
					$show_purchase_note     = $order_obj->has_status(
					// Purchases note on the order.
						apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) )
					);
					$get_order_currency   = get_woocommerce_currency_symbol( $order_obj->get_currency() );
					$refund_items_details = get_post_meta( $order_id, 'wps_rma_refund_items_details', true );
					foreach ( $order_obj->get_items() as $item_id => $item ) {
						$item_quantity = $item->get_quantity();
						$refund_qty    = $order_obj->get_qty_refunded_for_item( $item_id );
						$item_qty      = $item->get_quantity() + $refund_qty;

						if ( ! empty( $refund_items_details ) && isset( $refund_items_details[ $item_id ] ) ) {
							$return_item_qty = $refund_items_details[ $item_id ];
							$item_qty        = $item->get_quantity() - $return_item_qty;
						}

						if ( $item_qty > 0 ) {
							if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) {
								$variation_id = $item['variation_id'];
								$product_id   = $item['product_id'];
							} else {
								$product_id = $item['product_id'];
							}
							$product =
							// Get Product.
							apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );
							$thumbnail       = wp_get_attachment_image( $product->get_image_id(), 'thumbnail' );
							$coupon_discount = get_option( 'wps_rma_refund_deduct_coupon', 'no' );
							if ( 'on' === $coupon_discount ) {
								$tax_inc = $item->get_total() + $item->get_subtotal_tax();
								$tax_exc = $item->get_total() - $item->get_subtotal_tax();
							} else {
								$tax_inc = $item->get_subtotal() + $item->get_subtotal_tax();
								$tax_exc = $item->get_subtotal() - $item->get_subtotal_tax();
							}
							if ( empty( $wps_rma_check_tax ) ) {
								if ( 'on' === $coupon_discount ) {
									$wps_actual_price = $item->get_total();
								} else {
									$wps_actual_price = $item->get_subtotal();
								}
							} elseif ( 'wps_rma_inlcude_tax' === $wps_rma_check_tax ) {
								$wps_actual_price = $tax_inc;
							} elseif ( 'wps_rma_exclude_tax' === $wps_rma_check_tax ) {
								$wps_actual_price = $tax_exc;
							}
							$wps_total_actual_price += $wps_actual_price;
							$purchase_note           = get_post_meta( $product_id, '_purchase_note', true );
							?>
							<tr class="wps_rma_return_column" data-productid="<?php echo esc_html( $product_id ); ?>" data-variationid="<?php echo esc_html( $item['variation_id'] ); ?>" data-itemid="<?php echo esc_html( $item_id ); ?>">
								<?php
								// To show extra column field value in the tbody.
								do_action( 'wps_rma_add_extra_column_field_value', $item_id, $product_id );
								?>
								<td class="product-name">
									<input type="hidden" name="wps_rma_product_amount" class="wps_rma_product_amount" value="<?php echo esc_html( $wps_actual_price / $item->get_quantity() ); ?>">
									<div class="wps-rma-product__wrap">
										<?php
										$is_visible        = $product && $product->is_visible();
										$product_permalink =
										// Order item Permalink.
										apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order_obj );
										$thumbnail = wp_get_attachment_image( $product->get_image_id(), 'thumbnail' );
										if ( isset( $thumbnail ) && ! empty( $thumbnail ) ) {
											echo wp_kses_post( $thumbnail );
										} else {
											?>
											<img alt="Placeholder" width="150" height="150" class="attachment-thumbnail size-thumbnail wp-post-image" src="<?php echo esc_html( plugins_url() ); ?>/woocommerce/assets/images/placeholder.png">
										<?php } ?>
										<div class="wps_rma_product_title wps-rma__product-title">
											<?php
											// Woo Order Item Name.
											$o_n = apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item['name'] ) : $item['name'], $item, $is_visible );
											echo wp_kses_post( $o_n );
											// Quanity Html.
											$q_h = apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );
											echo wp_kses_post( $q_h );

											// Order Item meta Start.
											do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order_obj, true );
											if ( WC()->version < '3.0.0' ) {
												$order_obj->display_item_meta( $item );
												$order_obj->display_item_downloads( $item );
											} else {
												wc_display_item_meta( $item );
												wc_display_item_downloads( $item );
											}
											// Order Item meta End.
											do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order_obj, true );
											?>
											<p>
												<b><?php esc_html_e( 'Price', 'woo-refund-and-exchange-lite' ); ?> :</b> 
												<?php
													echo wp_kses_post( wps_wrma_format_price( $wps_actual_price / $item->get_quantity(), $get_order_currency ) );
												if ( 'wps_rma_inlcude_tax' === $wps_rma_check_tax ) {
													?>
													<small class="tax_label"><?php esc_html_e( '(incl. tax)', 'woo-refund-and-exchange-lite' ); ?></small>
													<?php
												} elseif ( 'wps_rma_exclude_tax' === $wps_rma_check_tax ) {
													?>
														<small class="tax_label"><?php esc_html_e( '(excl. tax)', 'woo-refund-and-exchange-lite' ); ?></small>
														<?php
												}
												?>
											</p>
										</div>
									</div>
								</td>
								<td class="product-quantity">
								<?php
								$allow_html = array(
									'input' => array(
										'type'     => array(),
										'value'    => array(),
										'class'    => array(),
										'name'     => array(),
										'disabled' => array(),
										'min'      => 1,
										'max'      => $item_qty,
									),
								);
								$qty_html   = '<input type="number" disabled value="' . esc_html( $item_qty ) . '" class="wps_rma_return_product_qty" name="wps_rma_return_product_qty">';
								echo // Refund form Quantity html.
								wp_kses( apply_filters( 'wps_rma_change_quanity', $qty_html, $item_qty ), $allow_html ); // phpcs:ignore
								?>
								</td>
								<td class="product-total">
									<?php
									echo wp_kses_post( wps_wrma_format_price( $wps_actual_price, $get_order_currency ) );

									if ( 'wps_rma_inlcude_tax' === $wps_rma_check_tax ) {
										?>
										<small class="tax_label"><?php esc_html_e( '(incl. tax)', 'woo-refund-and-exchange-lite' ); ?></small>
										<?php
									} elseif ( 'wps_rma_exclude_tax' === $wps_rma_check_tax ) {
										?>
											<small class="tax_label"><?php esc_html_e( '(excl. tax)', 'woo-refund-and-exchange-lite' ); ?></small>
											<?php
									}
									?>
								<input type="hidden" id="quanty" value="<?php echo esc_html( $item['qty'] ); ?>"> 
								</td>
							</tr>
							<?php if ( $show_purchase_note && $purchase_note ) : ?>
							<tr class="product-purchase-note">
								<td colspan="3"><?php echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) ); ?></td>
							</tr>
								<?php
							endif;
							?>
							<?php
						}
					}
					?>
					<tr>
						<th scope="row" colspan="<?php echo wps_rma_pro_active() ? '3' : '2'; ?>"><?php esc_html_e( 'Total Refund Amount', 'woo-refund-and-exchange-lite' ); ?></th>
						<td class="wps_rma_total_amount_wrap"><span id="wps_rma_total_refund_amount"><?php echo wp_kses_post( wps_wrma_format_price( $wps_total_actual_price, $get_order_currency ) ); ?></span>
						<input type="hidden" name="wps_rma_total_refund_price" class="wps_rma_total_refund_price" value="<?php echo esc_html( $wps_total_actual_price ); ?>">
							<?php
							if ( 'wps_rma_inlcude_tax' === $wps_rma_check_tax ) {
								?>
								<small class="tax_label"><?php esc_html_e( '(incl. tax)', 'woo-refund-and-exchange-lite' ); ?></small>
								<?php
							} elseif ( 'wps_rma_exclude_tax' === $wps_rma_check_tax ) {
								?>
									<small class="tax_label"><?php esc_html_e( '(excl. tax)', 'woo-refund-and-exchange-lite' ); ?></small>
									<?php
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="wps_rma_return_notification_checkbox" style="display:none"><img src="<?php echo esc_html( esc_url( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) ); ?>public/images/loading.gif" width="40px"></div>
		</div>
		<?php
		$predefined_return_reason = get_option( 'wps_rma_refund_reasons', '' );
		$predefined_return_reason = explode( ',', $predefined_return_reason );
		?>
		<div class="wps-rma-refund-request__row wps-rma-row__pd">
			<div class="wps-rma-col">
				<?php

				// Add someting after table on the refund request form.
				do_action( 'wps_rma_after_table', $order_id );
				$re_bank = get_option( 'wps_rma_refund_manually_de', false );
				if ( 'on' === $re_bank ) {
					?>
					<div id="bank_details">
					<textarea name="" class="wps_rma_bank_details" rows=4 id="wps_rma_bank_details" maxlength="1000" placeholder="<?php esc_html_e( 'Please Enter the bank details for manual refund', 'woo-refund-and-exchange-lite' ); ?>"></textarea>
					</div>
					<?php
				}
				?>
				<div class="wps_rma_subject_dropdown wps-rma-subject__dropdown">
					<div>
						<label>
							<b>
								<?php
									echo esc_html__( 'Subject of Refund Request', 'woo-refund-and-exchange-lite' );
								?>
							</b>
						</label>
						<span class="wps_field_mendatory">*</span>
					</div>
					<select name="wps_rma_return_request_subject" id="wps_rma_return_request_subject">
						<?php
						if ( ! empty( $predefined_return_reason[0] ) ) {
							foreach ( $predefined_return_reason as $predefine_reason ) {
								$predefine_reason = trim( $predefine_reason );
								?>
								<option value="<?php echo esc_html( $predefine_reason ); ?>"  <?php selected( $predefine_reason, $rr_subject ); ?>><?php echo esc_html( $predefine_reason ); ?></option>
								<?php
							}
						}
						?>
						<option value=""><?php esc_html_e( 'Other', 'woo-refund-and-exchange-lite' ); ?></option>
					</select>
				</div>
				<div class="wps_rma_other_subject">
					<input type="text" name="ced_rnx_return_request_subject" class="wps_rma_return_request_subject_text" id="wps_rma_return_request_subject_text" maxlength="5000" placeholder="<?php esc_html_e( 'Write your refund reason', 'woo-refund-and-exchange-lite' ); ?>">
				</div>
				<?php
				$predefined_return_desc = get_option( 'wps_rma_refund_description', false );
				if ( isset( $predefined_return_desc ) && 'on' === $predefined_return_desc ) {
					?>
					<div class="wps_rma_reason_description">
						<div>	
							<label>
								<b>
								<?php
								echo esc_html__( 'Description for Refund Reason', 'woo-refund-and-exchange-lite' );
								?>
								</b>
							</label>
							<span class="wps_field_mendatory">*</span>
						</div>
						<?php
						$predefined_return_reason_placeholder = get_option( 'wps_rma_refund_reason_placeholder', false );
						if ( empty( $predefined_return_reason_placeholder ) ) {
							$predefined_return_reason_placeholder = esc_html__( 'Write your description for a refund', 'woo-refund-and-exchange-lite' );
						}
						?>
						<textarea name="wps_rma_return_request_reason" cols="40" style="height: 222px;" class="wps_rma_return_request_reason" maxlength='10000' placeholder="<?php echo esc_html( $predefined_return_reason_placeholder ); ?>"><?php echo ! empty( $rr_reason ) ? esc_html( $rr_reason ) : ''; ?></textarea>
					</div>
					<?php
				}
				?>
				<?php
				// Add something above attachment on the refund request form.
				do_action( 'wps_rma_above_the_attachment' );
				?>
				<form action="" method="post" id="wps_rma_return_request_form" data-orderid="<?php echo esc_html( $order_id ); ?>" enctype="multipart/form-data">
					<?php
					$return_attachment = get_option( 'wps_rma_refund_attachment', false );
					$attach_limit      = get_option( 'wps_rma_attachment_limit', '15' );
					if ( empty( $attach_limit ) ) {
						$attach_limit = 5;
					}
					if ( isset( $return_attachment ) && ! empty( $return_attachment ) ) {
						if ( 'on' === $return_attachment ) {
							?>
							<div class="wps_rma_attach_files">
								<label><b><?php esc_html_e( 'Attach Files', 'woo-refund-and-exchange-lite' ); ?></b></label>
								<span class="wps_field_mendatory">*</span>
								<p>
									<span id="wps_rma_return_request_files">
									<input type="hidden" name="wps_rma_return_request_order" value="<?php echo esc_html( $order_id ); ?>">
									<input type="hidden" name="action" value="wps_rma_refund_upload_files">
									<input type="file" name="wps_rma_return_request_files[]" class="wps_rma_return_request_files">
									</span>
									<div><input type="button" value="<?php esc_html_e( 'Add More', 'woo-refund-and-exchange-lite' ); ?>" class="wps_rma_return_request_morefiles" data-count="1" data-max="<?php echo esc_html( $attach_limit ); ?>"></div>
									<i><?php esc_html_e( 'Only png,jpg,jpeg extension file is approved', 'woo-refund-and-exchange-lite' ); ?>.</i>
								</p>
							</div>
							<?php
						}
					}
					?>
					<div>
						<input type="submit" name="wps_rma_return_request_submit" value="<?php esc_html_e( 'Submit Request', 'woo-refund-and-exchange-lite' ); ?>" class="button btn">
						<div class="wps_rma_return_notification"><img src="<?php echo esc_html( esc_url( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) ); ?>public/images/loading.gif" width="40px"></div>
					</div>
				</form>
			</div>
				<?php
				$refund_rules_enable = get_option( 'wps_rma_refund_rules', 'no' );
				$refund_rules        = get_option( 'wps_rma_refund_rules_editor', '' );
				if ( isset( $refund_rules_enable ) && 'on' === $refund_rules_enable && ! empty( $refund_rules ) ) {
					?>
					<div class="wps-rma-col wps_rma_flex">        
						<div>
							<?php
								echo wp_kses_post( $refund_rules );
							?>
						</div>
					</div>
					<?php
				}
				?>
		</div>
		<div class="wps_rma_customer_detail">
			<?php
			wc_get_template( 'order/order-details-customer.php', array( 'order' => $order_obj ) );
			?>
		</div>
	</div>
	<?php
} else {
	if ( isset( $condition ) ) {
		echo wp_kses_post( $condition );
	} else {
		echo esc_html__( 'Refund Request Can\'t make on this order', 'woo-refund-and-exchange-lite' );
	}
}
$wps_wrma_show_sidebar_on_form =
// Side show/hide on refund request form.
apply_filters( 'wps_rma_refund_form_sidebar', true );
if ( $wps_wrma_show_sidebar_on_form ) {
	// Woo Main Content.
	do_action( 'woocommerce_after_main_content' );
}

get_footer( 'shop' );

