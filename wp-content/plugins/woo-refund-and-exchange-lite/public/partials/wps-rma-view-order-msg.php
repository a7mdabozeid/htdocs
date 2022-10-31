<?php
/**
 * The public-facing functionality of the plugin.
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
$flag = false;

if ( isset( $_GET['wps_rma_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['wps_rma_nonce'] ) ), 'wps_rma_nonce' ) && isset( $_GET['order_id'] ) ) {
	$order_id = sanitize_text_field( wp_unslash( $_GET['order_id'] ) );
}

$upload_attach = get_option( 'wps_rma_general_enable_om_attachment', 'no' );
get_header( 'shop' );

$wps_wrma_show_sidebar_on_form =
// Side show/hide on refund request form.
apply_filters( 'wps_rma_refund_form_sidebar', true );
if ( $wps_wrma_show_sidebar_on_form ) {
	// Before Main Content.
	do_action( 'woocommerce_before_main_content' );
}

if ( isset( $order_id ) && ! empty( $order_id ) ) {
	?>
<div class="wps_rma_order_msg_wrapper">
	<div class="wps_order_msg_notice_wrapper">
	</div>
	<div class="wps_order_msg_container">
		<form id="wps_order_new_msg_form" method="post" enctype="multipart/form-data">
			<input type="hidden" value="user" id="order_msg_type" name="order_msg_type">
			<div class="wps_order_msg_title">
				<h4><?php esc_html_e( 'Add a message', 'woo-refund-and-exchange-lite' ); ?></h4>
			</div>
			<textarea id="wps_order_new_msg" name="wps_order_new_msg" placeholder="<?php esc_html_e( 'Write a message you want to send to the Shop Manager.', 'woo-refund-and-exchange-lite' ); ?>" rows="6" maxlength='10000' required ></textarea>
			<div class="wps-order-msg-attachment-wrapper wps_rma_flex">
				<div class="wps-order-attachment">
					<?php if ( isset( $upload_attach ) && 'on' === $upload_attach ) : ?>
						<div><label for="wps_order_msg_attachment"> <?php esc_html_e( 'Attach file', 'woo-refund-and-exchange-lite' ); ?></label><span class="wps_field_mendatory"> *</span></div>
						<div><input type="file" id="wps_order_msg_attachment" name="wps_order_msg_attachment[]" multiple ></div>
						<div>
							<label><?php esc_html_e( 'Only png,jpg,jpeg extension file is approved', 'woo-refund-and-exchange-lite' ); ?>.</label>
						</div>
					<?php endif; ?>
				</div>
				<div class="wps-order-msg-btn">
					</div>
						<input type="submit" id="wps_order_msg_submit" name="wps_order_msg_submit" value="<?php esc_html_e( 'Send', 'woo-refund-and-exchange-lite' ); ?>" data-id="<?php echo esc_attr( $order_id ); ?>">
						<input 	type="hidden" name="wps_order_msg_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wps_order_msg_nonce' ) ); ?>"> 
					</div>
				</div>     
			</div>
		</form>
		<div class="wps_order_msg_reload_notice_wrapper">
			<p class="wps_order_msg_sent_notice"><strong><?php esc_html_e( 'Messages Refreshed Successfully', 'woo-refund-and-exchange-lite' ); ?></strong></p>
		</div>
		<div class="wps_order_msg_history_container">
			<div class="wps_order_msg_history_title">
			<h4 class="wps-order-heading wps_rma_flex"><?php esc_html_e( 'Message History', 'woo-refund-and-exchange-lite' ); ?>
				<a href="" class="wps_reload_messages"><img src="<?php echo esc_url( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . 'public/images/reload-icon.png'; ?>" class="reload-icon"></a>
			</h4>
			</div>
			<div class="wps_order_msg_sub_container">
				<?php
				$wps_order_messages = get_post_meta( $order_id, 'wps_cutomer_order_msg', true );
				if ( isset( $wps_order_messages ) && is_array( $wps_order_messages ) && ! empty( $wps_order_messages ) ) {
					foreach ( array_reverse( $wps_order_messages ) as $o_key => $o_val ) {
						foreach ( $o_val as $om_key => $om_val ) {
							$sender = ( 'Customer' === $om_val['sender'] ) ? esc_html__( 'Customer', 'woo-refund-and-exchange-lite' ) : esc_html__( 'Shop Manager', 'woo-refund-and-exchange-lite' );
							?>
							<div class="wps-order-msg__row">
								<div class="wps_order_msg_main_container wps_order_messages <?php echo ( 'Customer' === $om_val['sender'] ) ? 'wmb-order-customer__msg-container' : 'wmb-order-admin__msg-container'; ?>">
									<div class="wps_order_msg_sender_details">
										<span class="wps_order_msg_sender "><?php echo esc_html( $sender ); ?></span>
										<span class="wps_order_msg_date"><?php echo esc_html( get_date_from_gmt( gmdate( 'Y-m-d h:i a', $om_key ), 'Y-m-d h:i a' ) ); ?></span>
									</div>
								</div>
								<div class="wps_order_msg_detail_container">
									<span><?php echo esc_html( $om_val['msg'] ); ?></span>
								</div>
								<?php if ( isset( $om_val['files'] ) && ! empty( $om_val['files'] ) ) { ?>
									<hr>
									<div class="wps_order_msg_attach_container">
										<div class="wps_order_msg_attachments_title"><b><?php esc_html_e( 'Attachment :', 'woo-refund-and-exchange-lite' ); ?></b></div>
										<?php
										foreach ( $om_val['files'] as $fkey => $fval ) {
											if ( ! empty( $fval['name'] ) ) {
												$is_image = $fval['img'];
												?>
												<div class="wps_order_msg_single_attachment">
													<a target="_blank" href="<?php echo esc_url( get_home_url() ) . '/wp-content/attachment/' . esc_html( $order_id ) . '-' . esc_html( $fval['name'] ); ?>">
														<img class="wps_order_msg_attachment_thumbnail" src="<?php echo $is_image ? esc_url( get_home_url() ) . '/wp-content/attachment/' . esc_html( $order_id ) . '-' . esc_html( $fval['name'] ) : esc_url( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . '/public/images/attachment.png'; ?>">
														<span class="wps_order_msg_attachment_file_name"><?php echo esc_html( $fval['name'] ); ?></span>
													</a>
												</div>
											<?php } ?>
										<?php } ?>
									</div>
								<?php } ?>
							</div>
							<?php
						}
					}
				}
				?>
			</div>
		</div>
	</div>
	<?php
}
$wps_wrma_show_sidebar_on_form =
// Side show/hide on refund request form.
apply_filters( 'wps_rma_refund_form_sidebar', true );
if ( $wps_wrma_show_sidebar_on_form ) {
	// Woo Main Content.
	do_action( 'woocommerce_after_main_content' );
}

get_footer( 'shop' );
