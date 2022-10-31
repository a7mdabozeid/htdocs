<?php
/**
 * Abandoned cart template.
 *
 * @package Template
 */

if (! defined('ABSPATH') ) {
    exit;
}
$public   = new SA_Cart_Public(SMSALERT_PLUGIN_NAME_SLUG, SmsAlertConstants::SA_VERSION);
$image_id = esc_attr($args['image_id']);

$image_url = '';
if ($image_id ) {
    $image = wp_get_attachment_image_src($image_id, 'full');
    if (is_array($image) ) {
        $image_url = $image[0];
    }
}
?>
<div id="cart-exit-intent-form" class="<?php echo esc_attr($this->exit_intent_type()); ?>" style="background-color: rgba(0,0,0,0.5);">
    <div id="cart-exit-intent-form-container" style="background-color:<?php echo esc_attr($args['main_color']); ?>">
        <?php
        $kses_defaults = wp_kses_allowed_html('post');

        $svg_args = array(
                'svg'  => true,
                'line' => array(
                    'x1'           => true,
                    'y1'           => true,
                    'x2'           => true,
                    'y2'           => true,
                    'stroke'       => true,
                    'stroke-width' => true,
                ),
        );

        $allowed_tags = array_merge($kses_defaults, $svg_args);
		?>
		
		<div id="cart-exit-intent-close">
		<?php
        echo wp_kses(apply_filters('ab_cart_exit_intent_close_html', sprintf('<svg><line x1="1" y1="11" x2="11" y2="1" stroke="%s" stroke-width="2"/><line x1="1" y1="1" x2="11" y2="11" stroke="%s" stroke-width="2"/></svg>', esc_attr($args['inverse_color']), esc_attr($args['inverse_color']))), $allowed_tags);
		?>
		</div>
		<?php
	    if(!$args['enable_custom_page'])
	    {
	    ?>
        <div id="cart-exit-intent-form-content">
            <div id="cart-exit-intent-form-content-l">
                <?php echo wp_kses_post(apply_filters('ab_cart_exit_intent_image_html', sprintf('<img src="%s" alt="" title=""/>', esc_url($image_url)))); ?>
            </div>
            <div id="cart-exit-intent-form-content-r">
                <?php
                echo wp_kses_post(
                    apply_filters(
                        'ab_cart_exit_intent_title_html',
                        sprintf(
                            /* translators: %s: Exit Intent inverse color */
                            __('<h2 style="color: %s" >You were not leaving your cart just like that, right?</h2>', 'sms-alert'),
                            $args['inverse_color']
                        )
                    )
                );
                ?>
                <?php do_action('cart_exit_intent_after_title'); ?>
                <?php
                echo wp_kses_post(
                    apply_filters(
                        'ab_cart_exit_intent_description_html',
                        sprintf(
                            /* translators: %s: Exit Intent inverse color */
                            __('<p style="color: %s" >Just enter your mobile number below to save your shopping cart for later. And, who knows, maybe we will even send you a sweet discount code :)</p>', 'sms-alert'),
                            $args['inverse_color']
                        )
                    )
                );
                ?>
                <form>
                    <?php echo wp_kses_post(apply_filters('ab_cart_exit_intent_mobile_label_html', sprintf('<label for="cart-exit-intent-mobile" style="color: %s">%s</label>', $args['inverse_color'], __('Your Mobile No:', 'sms-alert')))); ?>
                   
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
                    
					
					
					
					<?php echo wp_kses_post(apply_filters('ab_cart_exit_intent_button_html', sprintf('<button type="submit" name="cart-exit-intent-submit" id="cart-exit-intent-submit" class="button" value="submit" style="background-color: %s; color: %s">%s</button>', $args['inverse_color'], esc_attr($args['main_color']), __('Save cart', 'sms-alert')))); 
				echo wp_nonce_field('smsalert_wp_abcart_nonce','smsalert_abcart_nonce', true, false);
				?>
                </form>
            </div>
        </div>
		<?php
		}
		else
		{
		 $post  = get_post(smsalert_get_option( 'cart_exit_intent_custom_page', 'smsalert_abandoned_cart', '' ),'ARRAY_A');
         echo !empty($post)?do_shortcode($post['post_content']):'';		 
		}
		?>
    </div>
	<?php
	if(!$args['enable_custom_page'])
	{
	?>
    <div id="cart-exit-intent-form-backdrop" style="background-color:<?php echo esc_attr($args['inverse_color']); ?>; opacity: 0;"></div>
	<?php
	}
	?>
</div>
