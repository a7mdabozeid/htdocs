<?php

namespace Razzi\Addons\Modules\Mega_Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class menu walker
 *
 * @package Razzi
 */
class Mobile_Walker extends \Walker_Nav_Menu {
	/**
	 * Store state of top level item
	 *
	 * @since 1.0.0
	 * @var boolean
	 */
	protected $in_mega = false;

	/**
	 * Background Item
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $style = '';

	/**
	 * Mega menu column
	 *
	 * @since 1.0.0
	 * @var int
	 */
	protected $column = 3;

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see   Walker::start_lvl()
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args An array of arguments. @see wp_nav_menu()
	 *
	 * @return string
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class=\"sub-menu check\">\n";
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see   Walker::end_lvl()
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args An array of arguments. @see wp_nav_menu()
	 *
	 * @return string
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent</ul>\n";
	}

	/**
	 * Start the element output.
	 * Display item description text and classes
	 *
	 * @see   Walker::start_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args An array of arguments. @see wp_nav_menu()
	 * @param int $id Current item ID.
	 *
	 * @return string
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$item_icon_type       = get_post_meta( $item->ID, 'tamm_menu_item_icon_type', true );
		$item_icon_svg        = get_post_meta( $item->ID, 'tamm_menu_item_icon_svg', true );
		$item_icon_image      = get_post_meta( $item->ID, 'tamm_menu_item_icon_image', true );
		$item_icon_color      = get_post_meta( $item->ID, 'tamm_menu_item_icon_color', true );
		$item_hide_text       = get_post_meta( $item->ID, 'tamm_menu_item_hide_text', true );

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$this->style = '';
		$this->class = '';

		// Container
		$class = array( 'mega-menu-content row-flex' );
		$attrs = ' class="' . esc_attr( join( ' ', $class ) ) . '"';

		$this->class = $attrs;

		// Svg
		$item_icon_html = '';
		if ( $item_icon_type === 'svg' ) {
			$item_icon_color = $item_icon_color ? 'style="color:' . $item_icon_color . '"' : '';
			$item_icon_html  = ! empty( $item_icon_svg ) ? '<span ' . $item_icon_color . ' class="razzi-svg-icon">' . \Razzi\Icon::sanitize_svg( $item_icon_svg ) . '</span> ' : '';
		} elseif ( ! empty( $item_icon_image ) ) {
			$item_icon_html = '<img src="' . esc_attr( $item_icon_image ) . '">';
		}

		/**
		 * Filter the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param array $args An array of arguments.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		/**
		 * Add active class for current menu item
		 */
		$active_classes = array(
			'current-menu-item',
			'current-menu-parent',
			'current-menu-ancestor',
		);
		$is_active      = array_intersect( $classes, $active_classes );
		if ( ! empty( $is_active ) ) {
			$classes[] = 'active';
		}

		if ( $item_hide_text ) {
			$classes[] = ' hide-text';
		}

		/**
		 * Filter the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param object $item The current menu item.
		 * @param array $args An array of {@see wp_nav_menu()} arguments.
		 * @param int $depth Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param object $item The current menu item.
		 * @param array $args An array of {@see wp_nav_menu()} arguments.
		 * @param int $depth Depth of menu item. Used for padding.
		 */


		$output .= $indent . '<li' . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		$atts['class'] = '';
		/**
		 * Add attributes for menu item link when this is not mega menu item
		 */
		if ( in_array( 'menu-item-has-children', $classes ) ) {
			$atts['class']         = 'dropdown-toggle';
			$atts['role']          = 'button';
			$atts['data-toggle']   = 'dropdown';
			$atts['aria-haspopup'] = 'true';
			$atts['aria-expanded'] = 'false';
		}

		if ( $item_icon_svg ) {
			$atts['class'] .= ' has-icon';
		}


		/**
		 * Filter the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *                       The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 * @type string $title Title attribute.
		 * @type string $target Target attribute.
		 * @type string $rel The rel attribute.
		 * @type string $href The href attribute.
		 * }
		 *
		 * @param object $item The current menu item.
		 * @param array $args An array of {@see wp_nav_menu()} arguments.
		 * @param int $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value      = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filter a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string $title The menu item's title.
		 * @param object $item The current menu item.
		 * @param array $args An array of {@see wp_nav_menu()} arguments.
		 * @param int $depth Depth of menu item. Used for padding.
		 */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output = ! empty( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $item_icon_html;
		$item_output .= ( ! empty( $args->link_before ) ? $args->link_before : '' ) . $title . ( ! empty( $args->link_after ) ? $args->link_after : '' );
		$item_output .= '</a>';
		$item_output .= ! empty( $args->after ) ? $args->after : '';

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item Menu item data object.
		 * @param int $depth Depth of menu item. Used for padding.
		 * @param array $args An array of {@see wp_nav_menu()} arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see   Walker::end_el()
	 *
	 * @since 1.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Page data object. Not used.
	 * @param int $depth Depth of page. Not Used.
	 * @param array $args An array of arguments. @see wp_nav_menu()
	 *
	 * @return string
	 */
	public function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}