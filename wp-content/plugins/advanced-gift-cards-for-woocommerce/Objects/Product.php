<?php

namespace AGCFW\Objects;

/**
 * Model that houses the data model of an advanced gift card object.
 *
 * @since 1.0
 */
class Product extends \WC_Product
{

    /**
     * Initialize advanced gift card product.
     *
     * @since 1.0
     * @access public
     *
     * @param WC_Product|int $product Product instance or ID.
     */
    public function __construct($product = 0)
    {
        parent::__construct($product);

        // allow ajax add to cart if gift card is not giftable.
        if (!$this->is_giftable()) {
            $this->supports[] = 'ajax_add_to_cart';
        }
    }

    /**
     * Get internal type.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     */
    public function get_type()
    {
        return 'advanced_gift_card';
    }

    /**
     * Get the add to url used mainly in loops.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     */
    public function add_to_cart_url()
    {
        $url = $this->is_purchasable() && $this->is_in_stock() && !$this->is_giftable() ? remove_query_arg(
            'added-to-cart',
            add_query_arg(
                array(
                    'add-to-cart' => $this->get_id(),
                ),
                (function_exists('is_feed') && is_feed()) || (function_exists('is_404') && is_404()) ? $this->get_permalink() : ''
            )
        ) : $this->get_permalink();
        return apply_filters('woocommerce_product_add_to_cart_url', $url, $this);
    }

    /**
     * Get the add to cart button text.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     */
    public function add_to_cart_text()
    {
        $text = $this->is_purchasable() && $this->is_in_stock() && !$this->is_giftable() ? __('Add to cart', 'advanced-gift-cards-for-woocommerce') : __('Select options', 'advanced-gift-cards-for-woocommerce');

        return apply_filters('woocommerce_product_add_to_cart_text', $text, $this);
    }

    /**
     * Get the add to cart button text description - used in aria tags.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     */
    public function add_to_cart_description()
    {
        /* translators: %s: Product title */
        $text = $this->is_purchasable() && $this->is_in_stock() && !$this->is_giftable() ? __('Add &ldquo;%s&rdquo; to your cart', 'advanced-gift-cards-for-woocommerce') : __('Read more about &ldquo;%s&rdquo;', 'advanced-gift-cards-for-woocommerce');

        return apply_filters('woocommerce_product_add_to_cart_description', sprintf($text, $this->get_name()), $this);
    }

    /**
     * Force product to be sold individually.
     *
     * @since 1.0
     * @access public
     *
     * @return bool Always true.
     */
    public function get_sold_individually($context = 'view')
    {
        return true;
    }

    /**
     * Get gift card data.
     *
     * @since 1.0
     * @since 1.1 Add gift card expiry data.
     * @access public
     */
    public function get_gift_card_data()
    {
        return array(
            'value'     => (float) $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_VALUE),
            'design'    => $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_DESIGN),
            'custom_bg' => $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_CUSTOM_BG),
            'expiry'    => $this->get_gift_card_expiry(),
        );
    }

    /**
     * Get the gift card value.
     * NOTE: This should be used for display purposes only.
     * 
     * @since 1.0
     * @access public
     * 
     * @return float Gift card value.
     */
    public function get_gift_card_value()
    {
        return (float) apply_filters('acfw_filter_amount', $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_VALUE));
    }

    /**
     * Get the attachment ID for the gift card design.
     * 
     * @since 1.0
     * @access public
     * 
     * @return int Attachment ID.
     */
    public function get_gift_card_design_attachment_id()
    {
        if ($custom_bg = $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_CUSTOM_BG)) {
            return (int) $custom_bg;
        }
        
        $design = $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_DESIGN);
        return AGCFW()->Helper_Functions->get_design_attachment_id($design);
    }

    /**
     * Get gift card design image source URL.
     *
     * @since 1.0
     * @access public
     *
     * @return string
     */
    public function get_gift_card_design_image_src()
    {
        if ($custom_bg = $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_CUSTOM_BG)) {
            $src = wp_get_attachment_image_url($custom_bg, 'full');
        } else {
            $design = $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_DESIGN);
            $src    = \AGCFW()->Helper_Functions->get_builtin_design_src($design);
        }

        return $src;
    }

    /**
     * Check if gift card product is giftable or not.
     *
     * @since 1.0
     * @access public
     *
     * @return bool True if giftable, false otherwise.
     */
    public function is_giftable()
    {
        return 'yes' === $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_IS_GIFTABLE);
    }

    /**
     * Override the get_image method to set the gift card design image as the product image displayed on catalog pages.
     * 
     * @since 1.0
     * @access public
     * 
     * @param string $size        Image size
     * @param array  $attr        Image attributes
     * @param bool   $placeholder Placeholder flag.
     * @return string Image markup.
     */
    public function get_image($size = 'woocommerce_thumbnail', $attr = array(), $placeholder = true)
    {
        $image = parent::get_image($size, $attr, $placeholder);

        if ($image === wc_placeholder_img( $size, $attr )) {
            $attach_id    = $this->get_gift_card_design_attachment_id();
            $design_image = wp_get_attachment_image($attach_id, $size, false, $attr);

            return apply_filters( 'woocommerce_product_get_image', $design_image, $this, $size, $attr, $placeholder, $design_image );
        }

        return $image;
    }

    /**
     * Get gift card expiry.
     * 
     * @since 1.1
     * @access public
     * 
     * @return string Expiry interval string.
     */
    public function get_gift_card_expiry()
    {
        $expiry = $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_EXPIRY);
        $expiry = $expiry ? $expiry : '5'; // default to 5 years.

        switch ($expiry) {
            case 'custom':
                $expiry_custom = $this->get_meta(\AGCFW()->Plugin_Constants->GIFT_CARD_EXPIRY_CUSTOM);
                $days = '1' === $expiry_custom ? 'day' : 'days';
                $expiry = sprintf('%s %s', $expiry_custom, $days);
                break;

            case 'noexpiry';
                break;
            
            default:
                $expiry .= ' years';
        }

        return $expiry;
    }
}
