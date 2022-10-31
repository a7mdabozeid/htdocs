

<div class="estp-wooproduct-wrapper estp-woocommerce-layout-4">
	<ul class="estp-product-lists-wrap estp-clearfix">	
        <li <?php post_class(); ?>>
        	
            <div class="estp-left-section">
          
                <div class="estp-wooproduct-image">

                    <?php if($show_atc_btn ==1 ){ ?>
                        <div class="estp-woocommerce-cart-btn">
                            <?php  $product = wc_get_product(get_the_ID()); ?>
                            <a class="add_to_cart_button" href="<?php echo  $product->add_to_cart_url(); ?>" target="_blank">
                                <?php 
                                $temp_woo_array = array('dashicons|dashicons-blank','fa|fa-blank','genericon|genericon-blank');
                                if(in_array($woocommerce_icon,$temp_woo_array))
                                {
                                    $woocommerce_icon = 'fa fa-shopping-cart';
                                }
                                else if($woocommerce_icon == '')
                                {
                                    $woocommerce_icon = 'fa fa-shopping-cart';
                                }
                                else if(!in_array($woocommerce_icon , $temp_woo_array))
                                {
                                    $woocommerce_icon = str_replace('|', ' ', $woocommerce_icon);
                                }
                                
                                ?>
                                <span>
                                    <i class="<?php echo $woocommerce_icon; ?>"></i>
                                    <?php _e('Add To Cart', ESTP_DOMAIN); ?>
                                </span>    
                            </a>
                        </div>
                    <?php } ?>


                    <a href="<?php the_permalink(); ?>">
                    <?php
                    if ( has_post_thumbnail() ) 
                    {
                        $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ),'thumbnail');
                        if ( ! empty( $large_image_url[0] ) ) 
                        { 
                            echo "<img src='".esc_url( $large_image_url[0] )."' alt='".the_title_attribute( array( 'echo' => 0 ) )."'/>";
                        }
                        else
                        {  
                            echo "<img src='".ESTP_IMAGE_DIR."/thumbnail-default.jpg' alt='thumbnail' width='200' height='200'/>";
                        }
                    }
                    else
                    {   
                        echo "<img src='".ESTP_IMAGE_DIR."/thumbnail-default.jpg' width='200' height='200' alt='thumbnail'/>";
                    } ?>
                    </a>                    
                </div>
            
            <!-- left section end -->
            </div>

        	<div class="estp-right-section">

                <a class="estp-product-title" href="<?php the_permalink(); ?>">
                	<?php woocommerce_template_loop_product_title(); ?>
                </a>

                <?php 
                    if($show_price == 1)
                    { 
                        // show price 
                        global $post;
                        $product = new WC_Product($post->ID); 
                        echo '<span>'. wc_get_price_including_tax($product).'</span>';
                    } 
                ?>
            </div> <!-- right section end -->
        </li>
	</ul>
</div>	