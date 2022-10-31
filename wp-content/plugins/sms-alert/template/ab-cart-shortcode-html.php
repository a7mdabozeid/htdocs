<div id="cart-exit-intent-form-content">
            <div id="cart-exit-intent-form-content-r">
                <form>
                    <?php echo wp_kses_post(apply_filters('ab_cart_exit_intent_mobile_label_html', sprintf('<label for="cart-exit-intent-mobile">%s</label>', __('Your Mobile No:', 'sms-alert')))); ?>
                   
				   <?php 
					global $allowedposttags;
			
					$allowedposttags['input'] = array(
							'type'      => array(),
							'name'      => array(),
							'value'     => array(),
							'class'   	=> array(),
							'id'   	    => array(),
							'required'  => array(),
							'size'  	=> array(),
						);
					
					echo wp_kses( apply_filters('ab_cart_exit_intent_mobile_field_html', '<input type="text" id="cart-exit-intent-mobile" class="phone-valid" size="20" required >'),$allowedposttags); ?>

					<?php echo wp_kses_post(apply_filters('ab_cart_exit_intent_button_html', sprintf('<button type="submit" name="cart-exit-intent-submit" id="cart-exit-intent-submit" class="button" value="submit">%s</button>', __('Save cart', 'sms-alert')))); ?>
                </form>
            </div>
        </div>