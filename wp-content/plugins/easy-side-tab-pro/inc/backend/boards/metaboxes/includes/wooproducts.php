<?php 
defined('ABSPATH') or die('No script kiddies please!!'); 
$product_type = array(
			        'category' => __('Category', ESTP_DOMAIN),
			        'latest_product' => __('Latest Product', ESTP_DOMAIN),
			        'upsell_product' => __('UpSell Product', ESTP_DOMAIN),
			        'feature_product' => __('Featured Product', ESTP_DOMAIN),
			        'on_sale' => __('On Sale Product', ESTP_DOMAIN)
			    );

$set_product_type = (isset($item['tab_content']['content_slider']['woocommerce_product']['product_type']) && $item['tab_content']['content_slider']['woocommerce_product']['product_type'] != '')?esc_attr($item['tab_content']['content_slider']['woocommerce_product']['product_type']):'category';

$args = array(
        'taxonomy'     => 'product_cat',
        'orderby'      => 'name',
        'show_count'   => 0,
        'pad_counts'   => 0,
        'hierarchical' => 1,
        'title_li'     => '',
        'hide_empty'   => 1
      );

$woocommerce_categories_obj = get_categories($args);
$woocommerce_categories = array();
$woocommerce_categories['all'] = 'Select Product Category';
foreach ($woocommerce_categories_obj as $category) 
{
    $woocommerce_categories[$category->term_id] = $category->name; //category[term id] = category_name
}

$product_list_category = (isset($item['tab_content']['content_slider']['woocommerce_product']['category']) && $item['tab_content']['content_slider']['woocommerce_product']['category'] != '')?$item['tab_content']['content_slider']['woocommerce_product']['category']:array();
?>

<h3><?php _e('WooCommerce Product',ESTP_DOMAIN);?></h3>

<div class="estp-field-wrap">
    <label><?php _e('Title Text', ESTP_DOMAIN); ?></label>
    <input type="text" name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][woocommerce_product][title_text]" value="<?php echo isset($item['tab_content']['content_slider']['woocommerce_product']['title_text'])?esc_attr($item['tab_content']['content_slider']['woocommerce_product']['title_text']):''; ?>" placeholder="Your Title">
</div>

<div class="estp-field-wrap">
    <label><?php _e('Product Type',ESTP_DOMAIN);?></label>
    
    <select name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][woocommerce_product][product_type]">
        <?php foreach ($product_type as $p_type => $type) { ?>
            <option value="<?php echo $p_type; ?>" <?php selected($p_type, $set_product_type); ?>>
            <?php echo $type; ?></option>
        <?php } ?>
    </select>
</div>

<div class="estp-field-wrap">
    <label><?php _e('Select Product Category',ESTP_DOMAIN);?></label>
    
    <select multiple name="tab[tab_settings][tab_items][<?php echo $key; ?>][tab_content][content_slider][woocommerce_product][category][]">
        <?php foreach ($woocommerce_categories as $c_type => $ctype) { ?>
            <option value="<?php echo $c_type; ?>" <?php if(in_array($c_type, $product_list_category)) echo "selected"; ?>>
            <?php echo $ctype; ?></option>
        <?php } ?>
    </select>
</div> 

<div class="estp-field-wrap">
	<label for="estp_show_price_<?php echo $key;?>" class="estp-field-label"><?php _e('Show Price',ESTP_DOMAIN);?></label>

    <label class="estp-field-content">
	    <input type="checkbox" id="estp_show_price_<?php echo $key;?>" class="estp_show_price" name="tab[tab_settings][tab_items][<?php echo $key;?>][tab_content][content_slider][woocommerce_product][show_price]" value="1" <?php echo (isset($item['tab_content']['content_slider']['woocommerce_product']['show_price']) && $item['tab_content']['content_slider']['woocommerce_product']['show_price'] == 1)?'checked':NULL; ?>/> 
	    <div class="estp-checkbox-style"></div>
   	</label>
</div> 

<div class="estp-field-wrap">
	<label for="estp_show_atc_btn_<?php echo $key;?>" class="estp-field-label"><?php _e('Show Add To Cart Button',ESTP_DOMAIN);?></label>

    <label class="estp-field-content">
	    <input type="checkbox" id="estp_show_atc_btn_<?php echo $key;?>" class="et_show_atc_btn" name="tab[tab_settings][tab_items][<?php echo $key;?>][tab_content][content_slider][woocommerce_product][show_atc_btn]" value="1" <?php echo (isset($item['tab_content']['content_slider']['woocommerce_product']['show_atc_btn']) && $item['tab_content']['content_slider']['woocommerce_product']['show_atc_btn'] == 1)?'checked':NULL; ?>/> 
	    <div class="estp-checkbox-style"></div>
   	</label>
</div>

<div class="estp-field-wrap">
    <label><?php _e('Posts Per Page',ESTP_DOMAIN);?></label>
     
    <input type="number" name="tab[tab_settings][tab_items][<?php echo $key;?>][tab_content][content_slider][woocommerce_product][posts_per_page]" value="<?php echo (isset($item['tab_content']['content_slider']['woocommerce_product']['posts_per_page']) && $item['tab_content']['content_slider']['woocommerce_product']['posts_per_page'] != '')?esc_attr($item['tab_content']['content_slider']['woocommerce_product']['posts_per_page']):'';?>"/> 
</div>

<div class="estp-field-wrap">
    <label><?php _e('Order By',ESTP_DOMAIN);?></label>
	<select name="tab[tab_settings][tab_items][<?php echo $key;?>][tab_content][content_slider][woocommerce_product][orderby]">
	  <?php $orderby = isset($item['tab_content']['content_slider']['woocommerce_product']['orderby'])?$item['tab_content']['content_slider']['woocommerce_product']['orderby']:NULL; ?>
	  <option value="id" <?php isset($orderby) ? selected('id', $orderby) : NULL; ?>><?php _e('ID', ESTP_DOMAIN); ?></option>
	  <option value="title" <?php isset($orderby) ? selected('title', $orderby) : NULL; ?>><?php _e('Title', ESTP_DOMAIN); ?></option>
	  <option value="name" <?php isset($orderby) ? selected('name', $orderby) : NULL; ?>><?php _e('Name', ESTP_DOMAIN); ?></option>
	  <option value="date" <?php isset($orderby) ? selected('date', $orderby) : NULL; ?>><?php _e('Date', ESTP_DOMAIN); ?></option>
	  <option value="rand" <?php isset($orderby) ? selected('rand', $orderby) : NULL; ?>><?php _e('Random Number', ESTP_DOMAIN); ?></option>
	  <option value="menu_order" <?php isset($orderby) ? selected('menu_order', $orderby) : NULL; ?>><?php _e('Menu Order', ESTP_DOMAIN); ?></option>
	  <option value="author" <?php isset($orderby) ? selected('author', $orderby) : NULL; ?>><?php _e('Author', ESTP_DOMAIN); ?></option>
	</select>
</div>

<div class="estp-field-wrap">
	<label><?php _e('Order',ESTP_DOMAIN);?></label>
	<?php $order = isset($item['tab_content']['content_slider']['woocommerce_product']['order'])?$item['tab_content']['content_slider']['woocommerce_product']['order']:NULL; ?>
	<select name="tab[tab_settings][tab_items][<?php echo $key;?>][tab_content][content_slider][woocommerce_product][order]">
	      <option value="asc" <?php isset($order)?( selected('asc', $order) ) : NULL; ?>><?php _e('Ascending Order',ESTP_DOMAIN);?></option>
	      <option value="desc" <?php isset($order)?( selected('desc', $order) ) : NULL; ?>><?php _e('Descending Order',ESTP_DOMAIN);?></option>  
	</select>
</div>