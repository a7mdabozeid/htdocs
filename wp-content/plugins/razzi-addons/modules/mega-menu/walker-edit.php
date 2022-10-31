<?php
/**
 * Customize and add more fields for mega menu
 */

namespace Razzi\Addons\Modules\Mega_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Walker_Nav_Menu_Edit' ) ) {
	require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
}

class Walker_Edit extends \Walker_Nav_Menu_Edit {
	/**
	 * Start the element output.
	 *
	 * @see   Walker_Nav_Menu::start_el()
	 * @since 3.0.0
	 *
	 * @global int $_wp_nav_menu_max_depth
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Not used.
	 * @param int $id Not used.
	 *
	 * @return string
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$item_type_icon  = get_post_meta( $item->ID, 'tamm_menu_item_icon_type', true );
		$item_icon_svg   = get_post_meta( $item->ID, 'tamm_menu_item_icon_svg', true );
		$item_icon_image = get_post_meta( $item->ID, 'tamm_menu_item_icon_image', true );
		$item_icon_color = get_post_meta( $item->ID, 'tamm_menu_item_icon_color', true );
		$item_hide_text  = get_post_meta( $item->ID, 'tamm_menu_item_hide_text', true );
		$item_is_label   = get_post_meta( $item->ID, 'tamm_menu_item_is_label', true );
		$item_content    = get_post_meta( $item->ID, 'tamm_menu_item_content', true );
		$item_mega       = get_post_meta( $item->ID, 'tamm_menu_item_mega', true );
		$item_mega_align = get_post_meta( $item->ID, 'tamm_menu_item_mega_align', true );
		$mega_width      = get_post_meta( $item->ID, 'tamm_menu_item_mega_width', true );

		$item_mega_background = wp_parse_args(
			get_post_meta( $item->ID, 'tamm_menu_item_background', true ),
			array(
				'image'      => '',
				'color'      => '',
				'attachment' => 'scroll',
				'size'       => '',
				'repeat'     => 'no-repeat',
				'position'   => array(
					'x'      => 'left',
					'y'      => 'top',
					'custom' => array(
						'x' => '',
						'y' => '',
					)
				)
			)
		);

		$item_output = '';
		parent::start_el( $item_output, $item, $depth, $args );

		$dom                  = new \DOMDocument();
		$dom->validateOnParse = true;
		$dom->loadHTML( mb_convert_encoding( $item_output, 'HTML-ENTITIES', 'UTF-8' ) );

		$xpath = new \DOMXPath( $dom );

		// Remove spaces in href attribute
		$anchors = $xpath->query( "//a" );

		foreach ( array_reverse( iterator_to_array( $anchors ) ) as $anchor ) {
			$anchor->setAttribute( 'href', trim( $anchor->getAttribute( 'href' ) ) );
		}

		// Add more menu item data
		$settings = $xpath->query( "//*[@id='menu-item-settings-" . $item->ID . "']" )->item( 0 );

		if ( $settings ) {
			$data = $dom->createElement( 'span' );
			$data->setAttribute( 'class', 'hidden tamm-data' );
			$data->setAttribute( 'data-mega', intval( $item_mega ) );
			$data->setAttribute( 'data-mega_align', esc_attr( $item_mega_align ) );
			$data->setAttribute( 'data-mega_width', esc_attr( $mega_width ) );
			$data->setAttribute( 'data-background', json_encode( $item_mega_background ) );
			$data->setAttribute( 'data-icon_image', esc_attr( $item_icon_image ) );
			$data->setAttribute( 'data-icon_type', esc_attr( $item_type_icon ) );
			$data->setAttribute( 'data-icon_color', esc_attr( $item_icon_color ) );
			$data->setAttribute( 'data-hide-text', intval( $item_hide_text ) );
			$data->setAttribute( 'data-is-label', intval( $item_is_label ) );
			$data->nodeValue = $item_content;

			$settings->appendChild( $data );

			$data           = $dom->createDocumentFragment();
			$item_icon_html = sprintf( '<span class="hidden tamm-data-icons">%s</span>', \Razzi\Icon::sanitize_svg( $item_icon_svg ) );
			$data->appendXML( $item_icon_html );
			$settings->appendChild( $data );

		}

		// Add settings link
		$cancel = $xpath->query( "//*[@id='cancel-" . $item->ID . "']" )->item( 0 );

		if ( $cancel ) {
			$link            = $dom->createElement( 'a' );
			$link->nodeValue = esc_html__( 'Settings', 'razzi' );
			$link->setAttribute( 'class', 'item-config-mega opensettings submitcancel hide-if-no-js' );
			$link->setAttribute( 'href', '#' );
			$sep            = $dom->createElement( 'span' );
			$sep->nodeValue = ' | ';
			$sep->setAttribute( 'class', 'meta-sep hide-if-no-js' );

			$cancel->parentNode->insertBefore( $link, $cancel );
			$cancel->parentNode->insertBefore( $sep, $cancel );
		}

		$output .= $dom->saveHTML();
	}
}
