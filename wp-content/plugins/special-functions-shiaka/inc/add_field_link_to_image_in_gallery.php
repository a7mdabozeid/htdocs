<?php
/**
 * Add custom media metadata fields
 *
 * Be sure to sanitize your data before saving it
 * https://codex.wordpress.org/Data_Validation
 *
 * @param $form_fields An array of fields included in the attachment form
 * @param $post The attachment record in the database
 * @return $form_fields The final array of form fields to use
 */
function add_image_attachment_fields_to_edit( $form_fields, $post ) {
	

	
	// Add a Credit field
	$form_fields["custom_image_link_sliderherohome"] = array(
		"label" => __("Custom Link", "shiaka"),
		"input" => "text", // this is default if "input" is omitted
		"value" => esc_attr( get_post_meta($post->ID, "_custom_image_link_sliderherohome", true) ),
		"helps" => __("The owner of the image."),
	);
	
	return $form_fields;
}
add_filter("attachment_fields_to_edit", "add_image_attachment_fields_to_edit", null, 2);