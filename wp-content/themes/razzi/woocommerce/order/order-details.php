<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}

$item_count = $order->get_item_count() - $order->get_item_count_refunded();
$item_text = esc_html__('Order items', 'razzi');

if ( $item_count == 1 ) {
	$item_text = esc_html__('Order item', 'razzi');
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<th class="woocommerce-orders-table__cell order-number">
					<div class="order-title"><?php esc_html_e('Order No:', 'razzi') ?></div>
					<span><?php echo esc_html( _x( '#', 'hash before order number', 'razzi' ) . $order->get_order_number() ); ?></span>
				</th>
				<th class="woocommerce-orders-table__cell order-date">
					<div class="order-title"><?php esc_html_e('Date:', 'razzi') ?></div>
					<span><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ) ?></span>
				</th>
				<th class="woocommerce-orders-table__cell order-status">
					<div class="order-title"><?php esc_html_e('Status:', 'razzi') ?></div>
					<span><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
				</th>
				<th class="woocommerce-orders-table__cell order-total">
					<div class="order-title"><?php esc_html_e('Order Amount:', 'razzi') ?></div>
					<span><?php echo wp_kses_post( sprintf( _n( '%1$s', '%1$s', $item_count, 'razzi' ), $order->get_formatted_order_total() ) ); ?></span>
				</th>
				<th class="woocommerce-orders-table__cell order-method">
					<div class="order-title"><?php esc_html_e('Payment method:', 'razzi') ?></div>
					<span><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></span>
				</th>
			</tr>
		</thead>

		<tbody>
			<tr class="woocommerce-table__line-item">
				<td class="woocommerce-table__product-table" colspan="5">
					<h2 class="woocommerce-order-details__title">
						<?php echo sprintf('%s (%s)', esc_html( $item_text ), esc_html( $item_count ) ); ?>
					</h2>
				</td>
			</tr>

			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>
	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<section class="woocommerce-order-total">
	<h2 class="woocommerce-order-downloads__title"><?php esc_html_e('Order Total', 'razzi') ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details order_total">
		<tbody>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'razzi' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
