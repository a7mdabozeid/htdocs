<?php

namespace FRFreeVendor;

/**
 * @template refund-meta-box.php
 * @var WC_Order $order ;
 */
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FormValuesRenderer;
?>
<div class="panel-wrap woocommerce">
	<div id="refund_data" class="panel woocommerce-refund-data">
		<div class="flex-wrapper">
			<div class="col col-table">
				<?php 
require_once __DIR__ . '/refund-table.php';
?>
			</div>
			<div class="col col-request">
				<h2><?php 
\esc_html_e('Refund Form', 'flexible-refund-and-return-order-for-woocommerce');
?></h2>
				<p class="description"><?php 
\esc_html_e('Below you will find the content from the return form fields', 'flexible-refund-and-return-order-for-woocommerce');
?></p>
				<?php 
$form_values = (new \FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\FormRenderer\FormValuesRenderer())->output($order);
if (!empty($form_values)) {
    echo $form_values;
} else {
    ?>
					<p class="description">
						<?php 
    \printf(\esc_html__('The return form has not been created yet, you can do it %1$shere &rarr;%2$s', 'flexible-refund-and-return-order-for-woocommerce'), '<a href="' . \admin_url('admin.php?page=wc-settings&tab=flexible_refunds&section=form') . '"><strong>', '</strong></a>');
    ?>
					</p>
					<?php 
}
require_once __DIR__ . '/table-footer.php';
?>
			</div>
		</div>
	</div>
</div>
<?php 
