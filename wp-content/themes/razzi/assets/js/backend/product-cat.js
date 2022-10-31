jQuery(document).ready(function ($) {
	"use strict";

	// Uploading files
	var file_frame,
		$banner_ids = $('#rz_cat_banners_id'),
		$cat_banner = $('#rz_cat_banner'),
		$cat_images = $cat_banner.find('.rz-cat-image');

	$cat_banner.on('click', '.upload_images_button', function (event) {
		var $el = $(this);

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if (file_frame) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media({
			multiple: true
		});

		// When an image is selected, run a callback.
		file_frame.on('select', function () {
			var selection = file_frame.state().get('selection'),
				attachment_ids = $banner_ids.val();

			selection.map(function (attachment) {
				attachment = attachment.toJSON();

				if (attachment.id) {
					attachment_ids = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
					var attachment_image = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;

					$cat_images.append('<li class="image" data-attachment_id="' + attachment.id + '"><img src="' + attachment_image + '" width="100px" height="100px" /><ul class="actions"><li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li></ul></li>');
				}

			});
			$banner_ids.val(attachment_ids);
		});


		// Finally, open the modal.
		file_frame.open();
	});

	// Image ordering.
	$cat_images.sortable({
		items               : 'li.image',
		cursor              : 'move',
		scrollSensitivity   : 40,
		forcePlaceholderSize: true,
		forceHelperSize     : false,
		helper              : 'clone',
		opacity             : 0.65,
		placeholder         : 'wc-metabox-sortable-placeholder',
		start               : function (event, ui) {
			ui.item.css('background-color', '#f6f6f6');
		},
		stop                : function (event, ui) {
			ui.item.removeAttr('style');
		},
		update              : function () {
			var attachment_ids = '';

			$cat_images.find('li.image').css('cursor', 'default').each(function () {
				var attachment_id = $(this).attr('data-attachment_id');
				attachment_ids = attachment_ids + attachment_id + ',';
			});

			$banner_ids.val(attachment_ids);
		}
	});

	// Remove images.
	$cat_banner.on('click', 'a.delete', function () {
		$(this).closest('li.image').remove();

		var attachment_ids = '';

		$cat_images.find('li.image').css('cursor', 'default').each(function () {
			var attachment_id = $(this).attr('data-attachment_id');
			attachment_ids = attachment_ids + attachment_id + ',';
		});

		$banner_ids.val(attachment_ids);

		return false;
	});

});
