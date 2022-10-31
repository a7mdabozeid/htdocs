<?php

namespace FRFreeVendor;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer;
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
$refund_meta = $order->get_meta('fr_refund_request_data');
$request_status = $order->get_meta('fr_refund_request_status');
$request_note = $order->get_meta('fr_refund_request_note');
?>
<div style="margin-bottom: 40px;">
	<h3 class="fr-myaccount-order-details-header"><?php 
\esc_html_e('Refund details', 'flexible-refund-and-return-order-for-woocommerce');
?></h3>

	<table class="td" cellspacing="0" cellpadding="0" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
		<tr>
			<th class="td" scope="col"><?php 
\esc_html_e('Product', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
			<th class="td" scope="col"><?php 
\esc_html_e('Cost', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
			<th class="td" scope="col"><?php 
\esc_html_e('Total', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
			<th class="td" scope="col"><?php 
\esc_html_e('Quantity to refund', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
			<th class="td" scope="col"><?php 
\esc_html_e('Refund Total', 'flexible-refund-and-return-order-for-woocommerce');
?></th>
		</tr>
		</thead>
		<tbody>
		<?php 
$total_refund_sum = 0.0;
foreach ($order_items as $item_id => $item) {
    /**
     * @var WC_Order_Item_Product $item
     */
    $product = $item->get_product();
    $is_checked = $refund_meta['items'][$item_id]['enabled'] ?? 'no';
    $qty = $refund_meta['items'][$item_id]['qty'] ?? 0;
    $item_price = ($item->get_subtotal() + $item->get_total_tax()) / $item->get_quantity() * (int) $qty;
    $total_refund_sum += $item_price;
    ?>
			<tr class="product_item">
				<td class="td">
					<?php 
    echo \esc_html($item->get_name());
    ?>
				</td>
				<td class="td">
					<?php 
    echo \wc_price($order->get_item_total($item, \true), ['currency' => $order->get_currency()]);
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
				</td>
				<td class="td">
					<?php 
    echo $order->get_formatted_line_subtotal($item);
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
				</td>
				<td class="td" style="width:160px;">
					<?php 
    echo \esc_html($qty);
    ?>
				</td>
				<td class="td">
					<?php 
    echo \wc_price($item_price, ['currency' => $order->get_currency()]);
    ?>
				</td>
			</tr>
		<?php 
}
?>
		<?php 
$shipping_items = $order->get_items('shipping');
foreach ($shipping_items as $shipping_item) {
    if ($show_shipping === 'yes') {
        /**
         * @var WC_Order_Item_Shipping $shipping_item
         */
        $shipping_total = (float) $shipping_item->get_total() + (float) $shipping_item->get_total_tax();
        if (isset($refund_meta['items'][$shipping_item->get_id()])) {
            $total_refund_sum += $shipping_item->get_total() + $shipping_item->get_total_tax();
        }
        ?>
				<tr class="td">
					<td class="td"><?php 
        echo \sprintf(\esc_html__('Shipping: %s', 'flexible-refund-and-return-order-for-woocommerce'), $shipping_item->get_name());
        ?></td>
					<td class="td"><?php 
        echo \wc_price((float) $shipping_item->get_total() + (float) $shipping_item->get_total_tax(), ['currency' => $order->get_currency()]);
        ?></td>
					<td class="td"><?php 
        echo \wc_price((float) $shipping_item->get_total() + (float) $shipping_item->get_total_tax(), ['currency' => $order->get_currency()]);
        ?></td>
					<td class="td">1</td>
					<td class="td"><span class="item-total-refund-qty"><?php 
        echo \wc_price($shipping_total, ['currency' => $order->get_currency()]);
        ?></span></td>
				</tr>
				<?php 
    }
}
?>
		</tbody>
		<tfoot>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td class="total-refund-amount"><?php 
echo \wc_price($total_refund_sum, ['currency' => $order->get_currency()]);
?></td>
		</tr>
		</tfoot>
	</table>
</div>

<?php 
