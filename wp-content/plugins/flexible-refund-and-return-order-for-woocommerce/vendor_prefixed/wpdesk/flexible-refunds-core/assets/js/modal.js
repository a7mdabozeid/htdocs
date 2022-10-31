(function ($) {
	jQuery('.confirm').click(function () {
		let confirm_id = jQuery(this).attr('data-confirm');
		let shadow = jQuery('.wpdesk-tooltip-shadow');
		let modal = jQuery('#' + confirm_id);
		shadow.show()
		modal.show()
	})

	jQuery('.close-modal').click(function () {
		jQuery('.wpdesk-tooltip-shadow').hide();
		jQuery(this).closest('.wpdesk-tooltip').hide();
		return false;
	})
})(jQuery);
