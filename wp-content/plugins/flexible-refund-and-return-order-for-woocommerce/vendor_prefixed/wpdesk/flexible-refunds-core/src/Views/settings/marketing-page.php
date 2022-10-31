<?php

namespace FRFreeVendor;

use FRFreeVendor\WPDesk\Library\Marketing\Boxes\MarketingBoxes;
/**
 * @var MarketingBoxes $boxes
 */
$boxes = $params['boxes'] ?? \false;
if (!$boxes) {
    return;
}
$support_url = \get_locale() === 'pl_PL' ? 'https://wpde.sk/flexible-refunds-get-support' : 'https://wpde.sk/flexible-refunds-get-support-pro';
?>
<div class="wrap">
	<div id="marketing-page-wrapper">
		<?php 
echo $boxes->get_boxes()->get_all();
?>

		<div class="marketing-buttons">
			<a class="button button-primary button-support confirm" data-confirm="confirm-support" href="#"><?php 
\esc_html_e('Get support', 'flexible-refund-and-return-order-for-woocommerce');
?></a>
		</div>

		<div class="wpdesk-tooltip-shadow"></div>
		<div id="confirm-support" class="wpdesk-tooltip wpdesk-tooltip-confirm">
			<span class="close-modal close-modal-button"><span class="dashicons dashicons-no-alt"></span></span>
			<h3><?php 
\esc_html_e('Before sending a message please:', 'flexible-refund-and-return-order-for-woocommerce');
?></strong></h3>
			<ul>
				<li><?php 
\esc_html_e('Prepare the information about the version of WordPress, WooCommerce, and Flexible Refund (preferably your system status from WooCommerce->Status)', 'flexible-refund-and-return-order-for-woocommerce');
?></li>
				<li><?php 
\esc_html_e('Describe the issue you have', 'flexible-refund-and-return-order-for-woocommerce');
?></li>
				<li><?php 
\esc_html_e('Attach any log files & printscreens of the issue', 'flexible-refund-and-return-order-for-woocommerce');
?></li>
			</ul>
			<div class="confirm-buttons">
				<a target="_blank" href="<?php 
echo \esc_url($support_url);
?>" class="confirm-url"><?php 
\esc_html_e('Ok, take me to support', 'flexible-refund-and-return-order-for-woocommerce');
?></a>
				<a href="#" class="close-confirm close-modal"><?php 
\esc_html_e('No, I\'ll wait', 'flexible-refund-and-return-order-for-woocommerce');
?></a>
			</div>
		</div>
	</div>
</div>
<?php 
