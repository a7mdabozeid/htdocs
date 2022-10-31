<?php

namespace FRFreeVendor;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses;
\defined('ABSPATH') || exit;
/**
 * @var WC_Order      $order
 * @var FieldRenderer $fields
 * @var string        $show_shipping
 */
if (!$order) {
    return;
}
$order_items = $order->get_items();
$order_items = $order->get_items();
$refund_meta = $order->get_meta('fr_refund_request_data');
$request_status = $order->get_meta('fr_refund_request_status');
$request_note = $order->get_meta('fr_refund_request_note');
if (\in_array($request_status, ['approved', 'rejected'])) {
    ?>
	<h2><?php 
    \printf(\esc_html__('Refund status: %s', 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::get_status_label($request_status));
    ?></h2>
	<?php 
    if (!empty($request_note)) {
        ?>
		<p><?php 
        echo $request_note;
        ?></p>
	<?php 
    }
}
?>

<form method="post" class="refund-front-form" action="" enctype="multipart/form-data">
	<section id="fr_refund_table" class="woocommerce-refund-details">
		<?php 
\do_action('wpdesk/fr/code/user-account/before-refund-table', $order);
?>
		<table class="woocommerce-table woocommerce-table-refund-details">
			<thead>
			<tr>
				<th class="product-name"><?php 
\esc_html_e('Product', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
				<th class="item-cost"><?php 
\esc_html_e('Cost', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
				<th class="item-total"><?php 
\esc_html_e('Total', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
				<th class="item-qty"><?php 
\esc_html_e('Quantity to refund', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
				<th class="item-total"><?php 
\esc_html_e('Refund Total', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
			</tr>
			</thead>
			<tbody>
			<?php 
$total_qty = 0;
$total_refund_sum = 0.0;
foreach ($order_items as $item_id => $item) {
    /**
     * @var WC_Order_Item_Product $item
     */
    $product = $item->get_product();
    $purchase_note = $product ? $product->get_purchase_note() : '';
    $qty = $item->get_quantity();
    $refunded_qty = $order->get_qty_refunded_for_item($item_id);
    if ($refunded_qty) {
        $qty = $qty - $refunded_qty * -1;
    }
    $total_qty += $qty;
    $total_refund_sum += $order->get_item_total($item, \true);
    ?>
				<tr class="product_item">
					<td class="item-name">
						<?php 
    echo \esc_html($item->get_name());
    ?>
					</td>
					<td class="item-cost">
						<?php 
    echo \wc_price($order->get_item_total($item, \true), ['currency' => $order->get_currency()]);
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
					</td>
					<td class="item-total">
						<?php 
    echo $order->get_formatted_line_subtotal($item, \true);
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
					</td>
					<td class="item-qty" style="width:160px;">
						<?php 
    if ($qty < 1) {
        ?>
							<span class="refunded-icon" style="vertical-align: bottom;">
								<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="12px" width="12px" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M212.333 224.333H12c-6.627 0-12-5.373-12-12V12C0 5.373 5.373 0 12 0h48c6.627 0 12 5.373 12 12v78.112C117.773 39.279 184.26 7.47 258.175 8.007c136.906.994 246.448 111.623 246.157 248.532C504.041 393.258 393.12 504 256.333 504c-64.089 0-122.496-24.313-166.51-64.215-5.099-4.622-5.334-12.554-.467-17.42l33.967-33.967c4.474-4.474 11.662-4.717 16.401-.525C170.76 415.336 211.58 432 256.333 432c97.268 0 176-78.716 176-176 0-97.267-78.716-176-176-176-58.496 0-110.28 28.476-142.274 72.333h98.274c6.627 0 12 5.373 12 12v48c0 6.627-5.373 12-12 12z"></path>
								</svg>
							</span>
							<?php 
        \esc_html_e('Refunded', 'flexible-refund-and-return-order-for-woocommerce');
        ?>
						<?php 
    } else {
        ?>
							<label>
								<input
									class="qty-input"
									step="1"
									type="number"
									min="0"
									max="<?php 
        echo \esc_attr($qty);
        ?>"
									value="0"
									name="fr_refund_form[items][<?php 
        echo \esc_attr($item_id);
        ?>][qty]"
									data-item-price="<?php 
        echo \esc_attr($order->get_item_total($item, \true));
        ?>"
								/>
							</label>
							<span class="product-quantity"><?php 
        echo \sprintf('&times;&nbsp;%s', $qty);
        ?></span>
						<?php 
    }
    ?>
					</td>
					<td class="item-total">
						<span class="item-total-refund-qty">0</span>
					</td>
				</tr>
			<?php 
}
?>
			<?php 
$shipping_total = 0;
if ($show_shipping === 'yes') {
    $shipping_items = $order->get_items('shipping');
    foreach ($shipping_items as $shipping_item) {
        /**
         * @var WC_Order_Item_Shipping $shipping_item
         */
        $refunded = \false;
        $shipping_total = (float) $shipping_item->get_total() + (float) $shipping_item->get_total_tax();
        $refunded_shipping_qty = $order->get_qty_refunded_for_item($shipping_item->get_id(), 'shipping');
        if ($refunded_shipping_qty === 1) {
            $total_qty += 0;
            $refunded = \true;
        } elseif ($shipping_total > 0) {
            $total_qty += \abs($shipping_item->get_quantity());
        }
        if ($shipping_total > 0) {
            $total_refund_sum += $shipping_total;
        }
        ?>
					<tr class="shipping-item">
						<td><?php 
        echo \sprintf(\esc_html__('Shipping: %s', 'flexible-refund-and-return-order-for-woocommerce'), $shipping_item->get_name());
        ?></td>
						<td></td>
						<td><?php 
        echo \wc_price($shipping_total, ['currency' => $order->get_currency()]);
        ?></td>
						<td class="item-qty">
							<?php 
        if ($refunded) {
            ?>
								<span class="refunded-icon" style="vertical-align: bottom;">
								<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="12px" width="12px" xmlns="http://www.w3.org/2000/svg">
									<path
										d="M212.333 224.333H12c-6.627 0-12-5.373-12-12V12C0 5.373 5.373 0 12 0h48c6.627 0 12 5.373 12 12v78.112C117.773 39.279 184.26 7.47 258.175 8.007c136.906.994 246.448 111.623 246.157 248.532C504.041 393.258 393.12 504 256.333 504c-64.089 0-122.496-24.313-166.51-64.215-5.099-4.622-5.334-12.554-.467-17.42l33.967-33.967c4.474-4.474 11.662-4.717 16.401-.525C170.76 415.336 211.58 432 256.333 432c97.268 0 176-78.716 176-176 0-97.267-78.716-176-176-176-58.496 0-110.28 28.476-142.274 72.333h98.274c6.627 0 12 5.373 12 12v48c0 6.627-5.373 12-12 12z"></path>
								</svg>
							</span>
								<?php 
            \esc_html_e('Refunded', 'flexible-refund-and-return-order-for-woocommerce');
            ?>
							<?php 
        } else {
            ?>
								<?php 
            if ($shipping_total > 0) {
                ?>
									<label>
										<input
											class="qty-input"
											type="checkbox"
											value="1"
											name="fr_refund_form[items][<?php 
                echo $shipping_item->get_id();
                ?>][qty]"
											data-item-price="<?php 
                echo \esc_attr((float) $shipping_item->get_total() + (float) $shipping_item->get_total_tax());
                ?>"
										/>
									</label>
								<?php 
            }
            ?>
							<?php 
        }
        ?>
						</td>
						<td><span class="item-total-refund-qty">0</span></td>
					</tr>
					<?php 
    }
}
?>
			<tr>
				<td></td>
				<td></td>
				<td><?php 
echo \wc_price($total_refund_sum, ['currency' => $order->get_currency()]);
?></td>
				<td>
					<span class="refund-total-qty">0</span> <?php 
echo \sprintf('&times;&nbsp;%s', $total_qty);
?>
					<input type="hidden" id="refund-total-qty-input" value="0" name="fr_refund_form[total_refund_qty]"/>
				</td>
				<td><span class="refund-total-calc">0</span></td>
			</tr>
			</tbody>
		</table>
		<p class="check-all-button">
			<button type="button"
					data-text-check="<?php 
\esc_attr_e('Check all', 'flexible-refund-and-return-order-for-woocommerce');
?>"
					data-text-uncheck="<?php 
\esc_attr_e('Uncheck all', 'flexible-refund-and-return-order-for-woocommerce');
?>"
					class="check"
			>
				<?php 
\esc_html_e('Check all', 'flexible-refund-and-return-order-for-woocommerce');
?>

			</button>
		</p>

		<?php 
\do_action('wpdesk/fr/code/user-account/after-refund-table', $order);
?>
		<div class="fr-request-form">
			<?php 
echo $fields->output();
?>
		</div>
		<p class="submit">
			<input type="submit" name="fr_refund_form[request_refund]" value="<?php 
\esc_attr_e('Send request', 'flexible-refund-and-return-order-for-woocommerce');
?>"">
			<?php 
\wp_nonce_field('fr_refund_request_send', 'fr_refund_form[fr_refund_request]');
?>
		</p>
		<?php 
\do_action('wpdesk/fr/code/user-account/after-request-form', $order, $fields);
?>
	</section>
</form>
<?php 
