<?php

namespace FRFreeVendor;

use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FieldRenderer;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses;
\defined('ABSPATH') || exit;
/**
 * @var WC_Order      $order
 * @var FieldRenderer $fields
 */
if (!$order) {
    return;
}
$order_items = $order->get_items();
$refund_meta = $order->get_meta('fr_refund_request_data');
$refund_status = $order->get_meta('fr_refund_request_status');
?>
<div class="fr-refund-order-meta-box-actions">
	<h2><?php 
\esc_html_e('Refund status', 'flexible-refund-and-return-order-for-woocommerce');
?></h2>
	<p class="description">
		<?php 
\esc_html_e('Once the refund is verified, add a note, select a status and send the information to the customer.', 'flexible-refund-and-return-order-for-woocommerce');
?>
	</p>
	<p class="current-status">
		<strong><?php 
\printf(\esc_html__('Current refund status: %s.', 'flexible-refund-and-return-order-for-woocommerce'), \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::get_status_label($refund_status));
?></strong>
	</p>
	<p>
		<textarea style="width:60%; height: 100px;" id="fr_refund_request_note" name="fr_refund_request[note]" class="regular-text" placeholder="<?php 
\esc_attr_e('Refund note', 'flexible-refund-and-return-order-for-woocommerce');
?>"></textarea>
	</p>
	<p>
		<select id="fr_refund_request_status" name="fr_refund_request[status]" class="regular-text">
			<option value=""><?php 
\esc_html_e('--- select status ---', 'flexible-refund-and-return-order-for-woocommerce');
?></option>
			<?php 
foreach (\FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Helpers\Statuses::get_statuses(['requested']) as $status_id => $status_name) {
    ?>
				<option <?php 
    \selected($refund_status, $status_name);
    ?> value="<?php 
    echo \esc_attr($status_id);
    ?>">
					<?php 
    echo \esc_html($status_name);
    ?></option>
				<?php 
}
?>
		</select>
		<button type="button" class="button button-primary fr-refund-button" name="save-requested-data"><?php 
\esc_html_e('Update &rarr;', 'flexible-refund-and-return-order-for-woocommerce');
?></button>
		<span class="spinner"></span>
	</p>
</div>
<?php 
