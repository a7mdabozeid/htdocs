<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;
$formatted_destination    = isset($formatted_destination) ? $formatted_destination : WC()->countries->get_formatted_address($package['destination'], ', ');
$has_calculated_shipping  = ! empty($has_calculated_shipping);
$show_shipping_calculator = ! empty($show_shipping_calculator);
$calculator_text          = '';
$index 					  = isset($index) ? $index : '';

global $woocommerce;
$i = 0;
$j = 0;
if(!empty($woocommerce->cart->get_cart()) && is_array($woocommerce->cart->get_cart())){
	foreach ($woocommerce->cart->get_cart() as $cart_item) {
		$cart_key = array_key_exists('key', $cart_item) ? $cart_item['key'] : '';
		$cart_unique_key = array_key_exists('unique_key', $cart_item) ? $cart_item['unique_key'] : '';
		$_product =  wc_get_product($cart_item['data']->get_id());

		// Check the product is vertual or downloadable.
		if ((!$_product-> is_virtual('yes')) && (!$_product->is_downloadable('yes'))) {

		//$item_id = $cart_item['id'];
		$cart_quantity = array_key_exists('quantity', $cart_item) ? $cart_item['quantity'] : '';
		$product_id = $_product->get_id();
		$product_price = $_product->get_price();
		$product_name = $_product->get_title();
		$user_id = get_current_user_id();
		$values = array();
		$ship_addresses = '';

		if(isset($cart_item['product_shipping_address']) && !empty($cart_item['product_shipping_address'])) {
			$ship_addresses = isset($cart_item['product_shipping_address']) ? $cart_item['product_shipping_address'] : '';


			$multi_address_change = urldecode(THMAF_Public::get_posted_value('multi_shipping_adr_data'));
			$multi_shipping_adr_data = json_decode($multi_address_change, true);
			
			$adr_data = '';
			$shipping_name = '';
			if(!empty($multi_shipping_adr_data) && is_array($multi_shipping_adr_data)) {
				foreach ($multi_shipping_adr_data as $key => $value) {
					if(array_key_exists($cart_key, $multi_shipping_adr_data)) {
						$shipping_name = $multi_shipping_adr_data[$cart_key]['address_name'];
						$ship_addresses = $shipping_name;
					}
				}
			}
			
			$settings_guest_usr = THMAF_Utils::get_setting_value('settings_guest_users');
			$enabled_guest_user = isset($settings_guest_usr['enable_guest_shipping']) ? $settings_guest_usr['enable_guest_shipping'] : '';
			if(is_user_logged_in()) {
				$adr_data = THMAF_Public::get_user_addresses($ship_addresses);	
			} else {
				if($enabled_guest_user == 'yes') {
					$ship_addresses = isset($cart_item['product_shipping_address']) ? $cart_item['product_shipping_address'] : '';
					$adr_data = THMAF_Utils::get_guest_user_addresses($ship_addresses);
				}
			}

			if($adr_data && is_array($adr_data)) {			
				$shipp_addr_format = THMAF_Utils::get_formated_address('shipping',$adr_data);
				if(apply_filters('thwma_inline_address_display', true)) {
					$separator = ', ';
					$pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format, $separator);
				} else {
					$pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format);
				}
				$shipping_country = isset($adr_data['shipping_country']) ? esc_attr($adr_data['shipping_country']) : '';
				$shipping_state = isset($adr_data['shipping_state']) ? esc_attr($adr_data['shipping_state']) : '';
				$shipping_postcode = isset($adr_data['shipping_postcode']) ? esc_attr($adr_data['shipping_postcode']) : '';
				$shipping_city = isset($adr_data['shipping_city'])?esc_attr($adr_data['shipping_city']):'';
				$shipping_address_1 = isset($adr_data['shipping_address_1']) ? esc_attr($adr_data['shipping_address_1']) : '';
				$shipping_address_2 = isset($adr_data['shipping_address_2']) ? esc_attr($adr_data['shipping_address_2']) : '';							
			    $active_methods   = array();

			    $values[] = array (
		    	 	'country' => $shipping_country,
	             	'amount'  => $product_price,
	             	'shipping_state' => $shipping_state,
	             	'shipping_postcode' => $shipping_postcode,
	             	'shipping_city' => $shipping_city,
	             	'shipping_address_1' => $shipping_address_1,
	             	'shipping_address_2' => $shipping_address_2
	            );
			}
		} else {
			$default_address = THMAF_Utils::get_default_address($user_id,'shipping');
			if($default_address && is_array($default_address)) {
				$shipp_addr_format = THMAF_Utils::get_formated_address('shipping',$default_address);
				if(apply_filters('thwma_inline_address_display', true)) {
					$separator = ', ';
					$pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format, $separator);
				} else {
					$pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format);
				}
				$shipping_country = isset($default_address['shipping_country']) ? esc_attr($default_address['shipping_country']) : '';
				$shipping_state = isset($default_address['shipping_state']) ? esc_attr($default_address['shipping_state']) : '';
				$shipping_postcode = isset($default_address['shipping_postcode']) ? esc_attr($default_address['shipping_postcode']) : '';
				$shipping_city = isset($default_address['shipping_city']) ? esc_attr($default_address['shipping_city']) : '';
				$shipping_address_1 = isset($default_address['shipping_address_1']) ? esc_attr($default_address['shipping_address_1']) : '';
				$shipping_address_2 = isset($default_address['shipping_address_2']) ? esc_attr($default_address['shipping_address_2']) : '';						
			    $active_methods   = array();
			    $values[] = array (
			    	'country' => $shipping_country,
	                'amount'  => $product_price,
	                'shipping_state' => $shipping_state,
	                'shipping_postcode' => $shipping_postcode,
	                'shipping_city' => $shipping_city,
	                'shipping_address_1' => $shipping_address_1,
	                'shipping_address_2' => $shipping_address_2
	            );
			}
		}
		$packages = WC()->shipping()->get_packages();
		$shipping_values = $values;
		WC()->shipping->calculate_shipping(THMAF_Utils::get_shipping_packages($shipping_values));
		$shipping_methods = WC()->shipping->packages;

		if(empty($shipping_methods)) {
			$default_address = THMAF_Utils::get_default_address($user_id,'shipping');
			if($default_address && is_array($default_address)) {
				$shipp_addr_format = THMAF_Utils::get_formated_address('shipping',$default_address);
				if(apply_filters('thwma_inline_address_display', true)) {
					$separator = ', ';
					$pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format, $separator);
				} else {
					$pdt_shipp_addr_formated = WC()->countries->get_formatted_address($shipp_addr_format);
				}
				$shipping_country = $default_address['shipping_country'];
				$shipping_state = $default_address['shipping_state'];
				$shipping_postcode = $default_address['shipping_postcode'];
				$shipping_city = $default_address['shipping_city'];
				$shipping_address_1 = $default_address['shipping_address_1'];
				$shipping_address_2 = $default_address['shipping_address_2'];							
			    $active_methods   = array();
			    $values[] = array (
			    	'country' => $shipping_country,
	                'amount'  => $product_price,
	                'shipping_state' => $shipping_state,
	                'shipping_postcode' => $shipping_postcode,
	                'shipping_city' => $shipping_city,
	                'shipping_address_1' => $shipping_address_1,
	                'shipping_address_2' => $shipping_address_2
	            );
			}
			$shipping_values = $values;
			WC()->shipping->calculate_shipping(THMAF_Utils::get_shipping_packages($shipping_values));
			$shipping_methods = WC()->shipping->packages;
		}

		$modified_active_methods = array();
		if(!empty($shipping_methods) && is_array($shipping_methods)) {
		    foreach($shipping_methods as $key => $methods) {
		    	if(isset($shipping_methods[$index]['rates'])) {
		    		$modified_active_methods[] = $shipping_methods[$index]['rates']; ?>
					<tr class="woocommerce-shipping-totals shipping thwma-woo-shipping-totals">
						<th><?php echo wp_kses_post('Ship to','themehigh-multiple-addresses'); ?></th>
						<td data-title="<?php echo esc_attr($package_name); ?>">
							<?php if ($modified_active_methods) :
								echo '<div class="ship-product-name"><b>'.esc_attr($product_name).' : </b></div>';
								echo '<div class="ship-address-data">'.$pdt_shipp_addr_formated.'</div>'; ?>

								<input type="hidden" name="ship_cart_key" class="ship-cart-key" value="<?php echo esc_attr($cart_key); ?>">
								<input type="hidden" name="ship_cart_unique_key" class="ship-cart-unique-key" value="<?php echo esc_attr($cart_unique_key); ?>">
								<input type="hidden" name="ship_product_id" class="ship-product-id" value="<?php echo esc_attr($product_id); ?>">
								<input type="hidden" name="ship_product_qty" class="ship-product-qty" value="<?php echo esc_attr($cart_quantity); ?>">
								  <?php //$ship_addr_format = serialize($shipp_addr_format);
								  $ship_addr_format = base64_encode(json_encode($shipp_addr_format)); ?>
								<input type="hidden" name="ship_address_formated" class="ship-address-formated" value="<?php echo esc_attr($ship_addr_format); ?>">
								<input type="hidden" name="ship_address_name" class="ship-address-name" value="<?php echo $ship_addresses; ?>">


								<ul id="shipping_method-<?php echo $j;?>" class="woocommerce-shipping-methods">
									<?php $active_methods   = array();
									$active_methods = $modified_active_methods;
									if(!empty($active_methods) && is_array($active_methods)) {
					        			foreach ($active_methods as $key => $methods) :
					        				$method_id = "";
					        				if(!empty($methods) && is_array($methods)) {
												foreach ($methods as $k => $method) :		        
						        					$method_id = $method->id; ?>
													<li>
														<?php $chosen_shipping_methodsg = WC()->session->get('chosen_shipping_methods');
														if(!empty($chosen_shipping_methodsg)) {
															if(isset($chosen_shipping_methodsg[$j])) {
																$checked = checked($method_id, $chosen_shipping_methodsg[$j], false);
															} else {
																$checked = "";
															}
														} else {
															$checked = "";
														}

														if (1 < count($methods)) {
															printf('<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', $i, esc_attr(sanitize_title($method_id)), esc_attr($method_id), $checked); // WPCS: XSS ok.
														} else {
															printf('<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" />', $i, esc_attr(sanitize_title($method_id)), esc_attr($method_id)); // WPCS: XSS ok.
														}
														printf('<label for="shipping_method_%1$s_%2$s">%3$s</label>', $i, esc_attr(sanitize_title($method_id)), wc_cart_totals_shipping_method_label($method)); // WPCS: XSS ok.
														do_action('woocommerce_after_shipping_rate', $method, $i); ?>
													</li>
												<?php endforeach;
											}
											$i++;
										endforeach;
									} ?>
								</ul>
							<?php elseif (! $has_calculated_shipping || ! $formatted_destination) :
								echo wp_kses_post(apply_filters('woocommerce_shipping_may_be_available_html', esc_html__('Enter your address to view shipping options.', 'woocommerce')));
							elseif (! is_cart()) :
								echo wp_kses_post(apply_filters('woocommerce_no_shipping_available_html', esc_html__('There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce')));
							else :
								// Translators: $s shipping destination.
								echo wp_kses_post(apply_filters('woocommerce_cart_no_shipping_available_html', sprintf(esc_html__('No shipping options were found for %s.', 'woocommerce') . ' ', '<strong>' . esc_html($formatted_destination) . '</strong>')));
								$calculator_text = esc_html__('Enter a different address', 'woocommerce');
							endif; ?>

							<?php if ($show_package_details) : ?>
								<?php //echo '<p class="woocommerce-shipping-contents"><small>' . esc_html($package_details) . '</small></p>'; ?>
							<?php endif; ?>

							<?php if ($show_shipping_calculator) : ?>
								<?php woocommerce_shipping_calculator($calculator_text); ?>
							<?php endif; ?>
						</td>
					</tr>
				<?php }
			}	
			$j++;
			//$default_shipping_methods = 'false';
		} else {
			//$default_shipping_methods = 'true';
		}

		} // Check the product is vertual or downloadable. 

	}
} ?> 
<tr>
	<?php do_action('thwma_cart_display_shipping_addresses'); ?>
</tr>