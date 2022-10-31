<?php

namespace WPML\CF7;

class Shortcodes implements \IWPML_Frontend_Action {

	public function add_hooks() {
		add_filter( 'shortcode_atts_wpcf7', [ $this, 'translate_shortcode_form_id' ] );
	}

	/**
	 * Find the right form and return it in the current language.
	 *
	 * @param array $atts Shortcode attributes to be filtered.
	 *
	 * @return array
	 */
	public function translate_shortcode_form_id( $atts ) {
		$form = null;

		if ( ! empty( $atts['id'] ) ) {
			$form = wpcf7_contact_form( (int) $atts['id'] );
		}

		if ( ! $form && ! empty( $atts['title'] ) ) {
			$form = wpcf7_get_contact_form_by_title( trim( $atts['title'] ) );
			unset( $atts['title'] );
		}

		if ( $form ) {
			$atts['id'] = apply_filters( 'wpml_object_id', $form->id(), Constants::POST_TYPE, true );
		}

		return $atts;
	}
}
