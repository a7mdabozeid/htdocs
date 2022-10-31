<?php

namespace FRFreeVendor;

use FRFreeVendor\WPDesk\Library\Marketing\RatePlugin\RateBox;
/**
 * @var RateBox $boxes
 */
$rate_box = $params['rate_box'] ?? \false;
if (!$rate_box) {
    return;
}
?>
<script id="fi_rate_box" type="text/template">
	<?php 
$is_PL = \get_locale() === 'pl_PL' ? 'https://wpdesk.pl' : 'https://wpdesk.net';
$review_link = 'https://wpde.sk/fi-footer-review-link';
echo $rate_box->render($review_link, \sprintf(
    // translator: %1$s icon,  %2$s open url tag, %3$s close url tag.
    \__('Created with %1$s by Heroes from %2$sWP Desk%3$s - if you like Flexible Refund rate us &rarr;', 'flexible-refund-and-return-order-for-woocommerce'),
    '<span class="love"><span class="dashicons dashicons-heart"></span></span>',
    '<a target="_blank" href="' . $is_PL . '">',
    '</a>'
));
?>
</script>
<script>
	(function ($) {
		let body_wrapper = $('#marketing-page-wrapper, #fiw-settings-footer');
		if (body_wrapper.length) {
			body_wrapper.append($('#fi_rate_box').html())
		}
	})(jQuery);
</script>
<?php 
