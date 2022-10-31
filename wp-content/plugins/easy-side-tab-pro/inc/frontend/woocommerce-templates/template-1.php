<div class="estp-wooproduct-wrapper estp-woocommerce-layout-1">
    
	<ul class="estp-product-lists-wrap estp-clearfix">	
        <li <?php post_class(); ?>>
        	
          
            <!-- show featured image -->
            <div class="estp-top-section estp-wooproduct-image">
                <a href="<?php the_permalink(); ?>">
                <?php
                if ( has_post_thumbnail() ) 
                {
                    $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ),'small');
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
        
        	<div class="estp-bottom-section">
          	    <a class="estp-product-title" href="<?php the_permalink(); ?>">
                	<?php woocommerce_template_loop_product_title(); ?>
                </a>

                <?php 
                if($show_price == 1)
                { 
                  // show price 
                  woocommerce_template_loop_price();
                }

                if($show_atc_btn == 1){ ?>
                    <!-- show add to cart -->
                  <?php woocommerce_template_loop_add_to_cart(); ?>
                <?php } ?>
            </div> <!-- right section end -->
        </li>
	</ul>
</div>	