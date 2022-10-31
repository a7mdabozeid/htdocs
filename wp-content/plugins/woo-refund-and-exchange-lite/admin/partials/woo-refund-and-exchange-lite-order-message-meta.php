<?php
/**
 * Exit if accessed directly
 *
 * @package woo-refund-and-exchange-lite
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

$order_obj = $theorder;
$order_id  = $order_obj->get_id();
?>
<div class="wps_order_msg_reload_notice_wrapper">
	<p class="wps_order_msg_sent_notice"><strong><?php esc_html_e( 'Messages Refreshed Successfully', 'woo-refund-and-exchange-lite' ); ?></strong></p>
</div>
<div class="wps_rma_admin_order_msg_wrapper">
	<div class="wps_admin_order_msg_history_title">
		<h4 class="wps_order_heading">
			<?php esc_html_e( 'Message History', 'woo-refund-and-exchange-lite' ); ?>
			<a href="" class="wps_reload_messages"><img src="<?php echo esc_url( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . '/public/images/reload-icon.png'; ?>" class="reload-icon"></a>
		</h4>
	</div>
	<div class="wps_admin_order_msg_history_container">
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
									<div class="wps_order_msg_attachments_title"><?php esc_html_e( 'Attachment:', 'woo-refund-and-exchange-lite' ); ?></div>
									<?php
									foreach ( $om_val['files'] as $fkey => $fval ) {
										if ( ! empty( $fval['name'] ) ) {
											$is_image = $fval['img'];
											?>
											<div class="wps_order_msg_single_attachment">
												<a target="_blank" href="<?php echo esc_url( get_home_url() ) . '/wp-content/attachment/' . esc_html( $order_id ) . '-' . esc_html( $fval['name'] ); ?>">
													<img class="wps_order_msg_attachment_thumbnail" src="<?php echo $is_image ? esc_url( get_home_url() ) . '/wp-content/attachment/' . esc_html( $order_id ) . '-' . esc_html( $fval['name'] ) : esc_url( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ) . '/admin/images/attachment.png'; ?>">
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
	<div class="wps_order_msg_notice_wrapper">
	</div>
	<div class="wps_admin_order_msg_container">
		<form id="wps_order_new_msg_form" method="post" enctype="multipart/form-data" action="">
			<input type="hidden" value="admin" id="order_msg_type" name="order_msg_type">	
			<div class="wps_order_msg_title"><h4 class="wps-order-heading"><?php esc_html_e( 'Add a message', 'woo-refund-and-exchange-lite' ); ?></h4></div>
				<textarea id="wps_order_new_msg" name="wps_order_new_msg" placeholder="<?php esc_html_e( 'Write a message you want to send to the Customer.', 'woo-refund-and-exchange-lite' ); ?>" maxlength="10000" rows="2"></textarea>
			<div class="wps-order-msg__attachment-lable">
				<label for="wps_order_msg_attachment"> <?php esc_html_e( 'Attach file', 'woo-refund-and-exchange-lite' ); ?>:</label>
			</div>
			<div class="wps-order-msg-attachment-wrapper wps_rma_flex">
				<input type="file" id="wps_order_msg_attachment" name="wps_order_msg_attachment[]" multiple >
				<div class="wps-order-msg-btn">
					<input type="submit" class="button button-primary" id="wps_order_msg_submit" value="<?php esc_html_e( 'Send', 'woo-refund-and-exchange-lite' ); ?>" name="wps_order_msg_submit" data-id="<?php echo esc_attr( $order_id ); ?>">
				</div>
			</div>	
		</form>
	</div>
</div>
<hr/>
<?php
