<?php

/*
  Plugin Name: Change Proceed To Checkout Text
  Plugin URI: https://arrowdesign.ie
  Description: A plugin for updating the proceed to checkout text. Change the proceed to checkout text to any text of your choosing. Your text will then be dislayed on the on the cart page button - instad of 'Proceed to checkout'.
  Version: 2.1
  Author: Arrow Design
  Author URI: https://arrowdesign.ie/change-proceed-to-checkout-text/
 */

// Exit if accessed directly
  if (!defined('ABSPATH'))
    exit;

/*
* Admin panel for saving proceed to checkout
* button text
*/
include_once 'admin/admin.php';

/*
* Hook function for showing updated
* text on the front end
*/




function arrowdesign_custom_proceed_to_checkout_update() {
	$terms = get_term_meta('2020', '_proceed_to_checkout_text', true);


		$checkout_url = WC()->cart->get_checkout_url();

		if ( ($terms =="") || (is_null($terms)) ) {
		//if $terms is not set or set to an empty string, revert to defaults
		}//end if

		else

		{
		$terms = esc_html ( $terms );
			remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
		  ?>
			   <a href="<?php echo $checkout_url; ?>" class="checkout-button button alt wc-forward"><?php  _e( $terms, 'woocommerce' ); ?></a>
		  <?php

		}//end else
	}//end function
add_filter( 'woocommerce_proceed_to_checkout', 'arrowdesign_custom_proceed_to_checkout_update', 10, 2 );

	//add settings link to plugin page
	function ad_ptct_plugin_settings_link($links) {
	  $settings_link_ad_pluginOptions = '<a href="options-general.php?page=ProceedToCheckoutText.php">Settings</a>';
	  array_unshift($links, $settings_link_ad_pluginOptions);
	  return $links;
	}
	$plugin_ad_bn = plugin_basename(__FILE__);
	add_filter("plugin_action_links_$plugin_ad_bn", 'ad_ptct_plugin_settings_link' );

	//add documentation link and support link to plugin page
	function ad_plugin_page_doc_meta( $ad_plugin_links, $file ) {
		if ( plugin_basename( __FILE__ ) == $file ) {
			$ad_plugin_row_meta = array(
			  'ad_ptct_docs'    => '<a href="' . esc_url( 'https://arrowdesign.ie/change-proceed-to-checkout-text/' ) . '" target="_blank" aria-label="' . esc_attr__( 'Plugin Additional Links', 'domain' ) . '" >' . esc_html__( 'Documentation', 'domain' ) . '</a>',

			'ad_ptct_support'    => '<a href="' . esc_url( 'https://arrowdesign.ie/contact-arrow-design-2/' ) . '" target="_blank" aria-label="' . esc_attr__( 'Plugin Additional Links', 'domain' ) . '" >' . esc_html__( 'Support', 'domain' ) . '</a>'


			);

	//		return array_merge( $ad_plugin_links, $ad_plugin_row_meta );
	//	}
	//	return (array) $links;

	return is_null($ad_plugin_links) ? $ad_plugin_row_meta : array_merge( $ad_plugin_links, $ad_plugin_row_meta );
	}
return (array) $ad_plugin_links;

	}

		add_filter( 'plugin_row_meta', 'ad_plugin_page_doc_meta', 10, 2 );





?>