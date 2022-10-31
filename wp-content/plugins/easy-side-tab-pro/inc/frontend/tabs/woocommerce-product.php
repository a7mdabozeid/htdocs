<?php 
defined('ABSPATH') or die("No script kiddies please!");  

$product_type = (isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['product_type']) && $pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['product_type'] != '')?$pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['product_type']:'category';

$category = array();
$category = (isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['category']) && $pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['category'] != '')?$pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['category']:'';

$show_price = (isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['show_price']) && $pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['show_price'] == 1)?1:0;

$show_atc_btn = (isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['show_atc_btn']) && $pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['show_atc_btn'] == 1)?1:0;

$posts_per_page = (isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['posts_per_page']) && $pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['posts_per_page'] != '')?$pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['posts_per_page']:'5';

$orderby = (isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['orderby']) && $pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['orderby'] != '')?$pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['orderby']:'';

$order = (isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['order']) && $pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['order'] != '')?$pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['order']:'asc';

$woocommerce_icon = ( isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['btn_icon']) && !empty($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['btn_icon']) )? esc_attr($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['btn_icon']):'';

$product_query_return_val = $this->get_products_category_wise('product', $product_type, $category, $orderby, $order, $posts_per_page);

$product_query = $product_query_return_val['product_query'];
$woocommerce_title_text = ( isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['title_text']) )? esc_attr($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['title_text']):'';
?>
	<div class="estp-front-tab-title">
        <h3><?php _e($woocommerce_title_text, ESTP_DOMAIN); ?></h3>
    </div>
    <?php
	if($product_query->have_posts()) 
	{ 
		while($product_query->have_posts()) 
		{ 
			$product_query->the_post();

			if(isset($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['layout']))
			{
				if($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['layout']=='woocommerce-layout-1')
				{
					include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/woocommerce-templates/template-1.php';
				}

				else if($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['layout']=='woocommerce-layout-2')
				{
					include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/woocommerce-templates/template-2.php';
				}

				else if($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['layout']=='woocommerce-layout-3')
				{
					include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/woocommerce-templates/template-3.php';
				}

				else if($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['layout']=='woocommerce-layout-4')
				{
					include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/woocommerce-templates/template-4.php';
				}

				else if($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['layout']=='woocommerce-layout-5')
				{
					include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/woocommerce-templates/template-5.php';
				}

				else if($pos_tab_settings['tab_content']['content_slider']['woocommerce_product']['layout']=='woocommerce-layout-6')
				{
					include ESTP_PLUGIN_ROOT_DIR.'inc/frontend/woocommerce-templates/template-6.php';
				}
			}	
	    } 
	} 
	else{
		echo "Sorry you don't have products for ".ucwords(str_replace('_', ' ', $product_query_return_val['product_type']));
	}
	wp_reset_query(); 
?>                    


