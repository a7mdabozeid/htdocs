<div class="estp-wooproduct-wrapper estp-woocommerce-layout-6">
	<ul class="estp-product-lists-wrap estp-clearfix">	
        <li <?php post_class(); ?>>
        	<div class="estp-recent-product-image-section estp-clearfix">
          
                <!-- show featured image -->
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
            
                <!-- left section end -->
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

                <?php if($show_atc_btn ==1 ){ ?>
                    <div class="estp-woocommerce-cart-btn">
                        <?php 
                            $product = wc_get_product(get_the_ID());
                        ?>
                        <a class="add_to_cart_button" href="<?php echo  $product->add_to_cart_url(); ?>" target="_self">
                            
                            <span>
                                <?php _e('Add To Cart', ESTP_DOMAIN); ?>
                            </span>    
                        </a>
                    </div>
                <?php } ?>
        	</div>
        </li>
	</ul>
</div>	