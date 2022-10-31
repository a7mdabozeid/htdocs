<?php
namespace AGCFW\Models;

use AGCFW\Abstracts\Abstract_Main_Plugin_Class;
use AGCFW\Helpers\Helper_Functions;
use AGCFW\Helpers\Plugin_Constants;
use AGCFW\Interfaces\Model_Interface;
use AGCFW\Objects\Advanced_Gift_Card;
use AGCFW\Interfaces\Activatable_Interface;
use AGCFW\Objects\Product;

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly

/**
 * Model that houses the logic of the Product_Type module.
 *
 * @since 1.0
 */
class Product_Type implements Model_Interface, Activatable_Interface
{

    /*
    |--------------------------------------------------------------------------
    | Class Properties
    |--------------------------------------------------------------------------
     */

    /**
     * Property that holds the single main instance of URL_Coupon.
     *
     * @since 1.0
     * @access private
     * @var Product_Type
     */
    private static $_instance;

    /**
     * Model that houses all the plugin constants.
     *
     * @since 1.0
     * @access private
     * @var Plugin_Constants
     */
    private $_constants;

    /**
     * Property that houses all the helper functions of the plugin.
     *
     * @since 1.0
     * @access private
     * @var Helper_Functions
     */
    private $_helper_functions;

    /**
     * Property that houses the list of gift card designs to be inserted as attachments.
     * 
     * @since 1.0
     * @access private
     * @var array
     */
    private $_designs_to_insert = array();

    /*
    |--------------------------------------------------------------------------
    | Class Methods
    |--------------------------------------------------------------------------
     */

    /**
     * Class constructor.
     *
     * @since 1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     */
    public function __construct(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        $this->_constants        = $constants;
        $this->_helper_functions = $helper_functions;

        $main_plugin->add_to_all_plugin_models($this);
        $main_plugin->add_to_public_models($this);
    }

    /**
     * Ensure that only one instance of this class is loaded or can be loaded ( Singleton Pattern ).
     *
     * @since 1.0
     * @access public
     *
     * @param Abstract_Main_Plugin_Class $main_plugin      Main plugin object.
     * @param Plugin_Constants           $constants        Plugin constants object.
     * @param Helper_Functions           $helper_functions Helper functions object.
     * @return Product_Type
     */
    public static function get_instance(Abstract_Main_Plugin_Class $main_plugin, Plugin_Constants $constants, Helper_Functions $helper_functions)
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($main_plugin, $constants, $helper_functions);
        }

        return self::$_instance;
    }

    /**
     * Insert gift card designs upon plugin activation as attachments.
     * This function needs to run on init after WC has completely loaded.
     * 
     * @since 1.0
     * @since 1.1 Changed to private access and run on activation directly.
     * @access private
     */
    private function _insert_gift_card_designs_as_attachment()
    {
        $design_attachments = get_option($this->_constants->DESIGN_ATTACHMENTS, array());

        if (is_array($design_attachments) && !empty($design_attachments)) {
            return;
        }

        $designs    = array('default', 'birthday', 'thankyou');
        $upload_dir = wp_upload_dir();

        // create AGCFW designs directory.
        wp_mkdir_p($upload_dir['basedir'] . '/agcfw/');

        $designs_to_insert = array();
        foreach ($designs as $design) {
            $source     = $this->_helper_functions->get_builtin_design_path($design);
            $filename   = str_replace($this->_constants->IMAGES_ROOT_PATH, $upload_dir['basedir'] . '/agcfw/', $source);

            // copy design image to the uploads directory.
            if ( ! file_exists( $filename ) ) {
                copy( $source, $filename );
            }

            $filetype                   = wp_check_filetype(basename($filename), null);
            $designs_to_insert[$design] = array(
                'filename'   => $filename,
                'attachment' => array(
                    'guid'           => $upload_dir['baseurl'] . '/agcfw/' . basename( $filename ),
                    'post_mime_type' => $filetype['type'],
                    'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                )
            );
        }

        // skip if there are no designs to insert.
        if (empty($designs_to_insert)) {
            return;
        }

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $design_attachments = array();
        foreach ($designs_to_insert as $design => $data) {
            
            $attach_id = wp_insert_attachment($data['attachment'], $data['filename']);
            if (is_wp_error($attach_id)) {
                continue;
            }

            $design_attachments[$design] = $attach_id;

            // Generate the metadata for the attachment, and update the database record.
            $attach_data = wp_generate_attachment_metadata( $attach_id, $data['filename'] );
            wp_update_attachment_metadata( $attach_id, $attach_data );
        }
        
        if (is_array($design_attachments) && !empty($design_attachments)) {
            update_option($this->_constants->DESIGN_ATTACHMENTS, $design_attachments);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Edit product interface (Admin)
    |--------------------------------------------------------------------------
     */

    /**
     * Register product type in the select field option.
     *
     * @since 1.0
     * @access public
     *
     * @param array $product_types List of product types.
     * @return array Filtered list of product types.
     */
    public function register_product_type_option($product_types)
    {
        $product_types['advanced_gift_card'] = __('Advanced Gift Card', 'advanced-gift-cards-for-woocommerce');
        return $product_types;
    }

    /**
     * Override product data tabs to add custom classes.
     *
     * @since 1.0
     * @access public
     *
     * @param array $tabs List of product tabs.
     * @param return Filter list of product tabs.
     */
    public function override_product_data_tabs($tabs)
    {
        // $tabs['general']['class'][] = 'show_if_advanced_gift_card';

        $tabs['inventory']['class'][] = 'show_if_advanced_gift_card';
        $tabs['shipping']['class'][]  = 'hide_if_advanced_gift_card';

        return $tabs;
    }

    /**
     * Register product class.
     *
     * @since 1.0
     * @access public
     *
     * @param string $classname    Product classname.
     * @param string $product_type Product type.
     * @return string Filtered classname.
     */
    public function register_product_class($classname, $product_type)
    {
        if ('advanced_gift_card' === $product_type) {
            return "\AGCFW\Objects\Product";
        }

        return $classname;
    }

    /**
     * Override the product creation script in WC REST API process.
     * 
     * @since 1.0
     * @access public
     * 
     * @param WC_Product      $product Product object.
     * @param WP_REST_Request $request Request object.
     * @return WC_Product Filtered product object.
     */
    public function override_product_type_in_wc_rest_api_pre_insert($product, $request)
    {
        if (isset($request['type']) && 'advanced_gift_card' === $request['type']) {
            $product->apply_changes();
            
            $gc_product = new Product();

            // get all product data and set one by one in gift card product object.
            foreach ($product->get_data() as $key => $value) {
                if ('meta_data' !== $key && \method_exists($gc_product, 'set_' . $key)) {
                    $method = 'set_' . $key;
                    $gc_product->$method($value);
                }
            }

            // get all meta data and set one by one in gift card product object.
            foreach ($product->get_meta_data() as $meta) {
                $gc_product->add_meta_data($meta->key, $meta->value);
            }
            
            return $gc_product;
        }

        return $product;
    }

    /**
     * Display gift card related fields.
     *
     * @since 1.0
     * @since 1.1 add gift card expiry fields.
     * @access public
     *
     * @global WC_Product $product_object Product object.
     */
    public function display_gift_card_fields()
    {
        global $product_object;

        $value         = $product_object->get_meta($this->_constants->GIFT_CARD_VALUE, true, 'edit');
        $is_giftable   = $product_object->get_meta($this->_constants->GIFT_CARD_IS_GIFTABLE, true, 'edit');
        $expiry        = $product_object->get_meta($this->_constants->GIFT_CARD_EXPIRY, true, 'edit');
        $expiry_custom = $product_object->get_meta($this->_constants->GIFT_CARD_EXPIRY_CUSTOM, true, 'edit');
        $design        = $product_object->get_meta($this->_constants->GIFT_CARD_DESIGN, true, 'edit');
        $custom_bg     = $product_object->get_meta($this->_constants->GIFT_CARD_CUSTOM_BG, true, 'edit');

        include $this->_constants->VIEWS_ROOT_PATH . 'view-gift-card-options-group.php';
    }

    /**
     * Save values for the gift card related fields.
     *
     * @since 1.0
     * @since 1.1 Save gift card expiry data.
     * @access public
     *
     * @param WC_Product $product Product object.
     */
    public function save_gift_card_fields($product)
    {
        $post_data = isset($_POST['agcfw']) ? $_POST['agcfw'] : array();

        if (empty($post_data) || 'advanced_gift_card' !== $product->get_type()) {
            return;
        }

        if (isset($post_data['value'])) {
            $product->update_meta_data($this->_constants->GIFT_CARD_VALUE, $this->_helper_functions->sanitize_price($post_data['value']));
        }

        $is_giftable = isset($post_data['is_giftable']) ? 'yes' : '';
        $product->update_meta_data($this->_constants->GIFT_CARD_IS_GIFTABLE, $is_giftable);

        $expiry = isset($post_data['expiry']) ? sanitize_text_field($post_data['expiry']) : '5';
        $product->update_meta_data($this->_constants->GIFT_CARD_EXPIRY, $expiry);

        if ('custom' === $expiry && isset($post_data['expiry_custom'])) {
            $product->update_meta_data($this->_constants->GIFT_CARD_EXPIRY_CUSTOM, sanitize_text_field($post_data['expiry_custom']));
        }

        if (isset($post_data['design'])) {
            $product->update_meta_data($this->_constants->GIFT_CARD_DESIGN, sanitize_text_field($post_data['design']));
        }

        if (isset($post_data['custom_bg'])) {
            $product->update_meta_data($this->_constants->GIFT_CARD_CUSTOM_BG, sanitize_text_field($post_data['custom_bg']));
        }

        // gift card products are always virtual.
        $product->set_virtual(true);

        $product->save_meta_data();
    }

    /*
    |--------------------------------------------------------------------------
    | Product frontend view
    |--------------------------------------------------------------------------
     */

    /**
     * Display single product form.
     *
     * @since 1.0
     * @access public
     *
     * @global AGCFW\Objects\Product $product Product object.
     */
    public function display_single_product_form()
    {
        global $product;

        $this->_helper_functions->load_template(
            'agcfw-single-product/add-to-cart.php',
            array(
                'send_to_default' => 'friend',
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Order item details (Admin and frontend)
    |--------------------------------------------------------------------------
     */

    /**
     * Format gift card item details displayed in the edit order page so it will display a more friendly version of the metadata keys.
     *
     * @since 1.0
     * @access public
     *
     * @param string $display_key Display key/label.
     * @param object $meta        Meta data.
     */
    public function format_order_item_display_meta_keys($display_key, $meta)
    {
        switch ($meta->key) {
            case $this->_constants->GIFT_CARD_SEND_TO_META:
                $display_key = __('Send to', 'advanced-gift-cards-for-woocommerce');
                break;
            case $this->_constants->GIFT_CARD_RECIPIENT_NAME_META:
                $display_key = __('Recipient name', 'advanced-gift-cards-for-woocommerce');
                break;
            case $this->_constants->GIFT_CARD_RECIPIENT_EMAIL_META:
                $display_key = __('Recipient email', 'advanced-gift-cards-for-woocommerce');
                break;
            case $this->_constants->GIFT_CARD_SHORT_MESSAGE_META:
                $display_key = __('Short message', 'advanced-gift-cards-for-woocommerce');
                break;
            case $this->_constants->GIFT_CARD_ENTRY_ID_META:
                $display_key = __('Gift card code', 'advanced-gift-cards-for-woocommerce');
                break;
            case $this->_constants->EMAIL_ALREADY_SENT_META:
                $display_key = __('Email sent', 'advanced-gift-cards-for-woocommerce');
                break;
        }

        return $display_key;
    }

    /**
     * Format gift card item values displayed in the edit order page.
     *
     * @since 1.0
     * @access public
     *
     * @param string $display_value Display value.
     * @param object $meta          Meta data.
     */
    public function format_order_item_display_meta_values($display_value, $meta)
    {
        if ($this->_constants->GIFT_CARD_ENTRY_ID_META === $meta->key) {
            $gift_card     = new Advanced_Gift_Card($meta->value);
            $display_value = $gift_card->get_code();
        }

        if(strpos($meta->key, 'agcfw_') !== false) {
            $display_value = wp_unslash($display_value);
        }

        return $display_value;
    }

    /**
     * Append gift card expire value on order item meta.
     * 
     * @since 1.1
     * @access public
     * 
     * @param array         $formatted_meta List of formatted metadata for the order item.
     * @param WC_Order_Item $order_item     Order item object.
     * @return array Filtered list of formatted metadata for the order item.
     */
    public function append_gift_card_expire_value_on_order_item_meta($formatted_meta, $order_item)
    {
        $gift_card_meta = array_filter($formatted_meta, function($m) {
            return $m->key === $this->_constants->GIFT_CARD_ENTRY_ID_META;
        });

        if (!empty($gift_card_meta)) {
            $gift_card   = new Advanced_Gift_Card(current($gift_card_meta)->value);
            $date_expire = $gift_card->get_date('date_expire');

            $formatted_meta[] = (object) array(
                'key'           => $this->_constants->GIFT_CARD_ENTRY_ID_META . '_expiry',
                'value'         => '',
                'display_key'   => __('Gift card expire date', 'advanced-gift-cards-for-woocommerce'),
                'display_value' => $date_expire ? $date_expire->format($this->_helper_functions->get_wp_datetime_format()) : __('No expiry', 'advanced-gift-cards-for-woocommerce'),
            );
        }

        return $formatted_meta;
    }

    /**
     * Set the gift card image as the product placeholder image on the single product page.
     * 
     * @since 1.0
     * @access public
     * 
     * @param string $src Image src.
     * @return string Filtered image src.
     */
    public function set_gift_card_image_as_product_placeholder_image($src)
    {
        global $product;

        if ($product instanceof Product && is_single()) {
            $attach_id = $product->get_gift_card_design_attachment_id();
            $src       = wp_get_attachment_image_url($attach_id, 'full');
        }

        return $src;
    }

    /**
     * Display gift card value template.
     * 
     * @since 1.0
     * @access public
     */
    public function display_gift_card_value_template()
    {
        global $product;

        if ($product instanceof Product) {
            $this->_helper_functions->load_template(
                'agcfw-single-product/gift-card-value.php'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @since 1.0
     * @access public
     * @implements AGCFW\Interfaces\Initializable_Interface
     */
    public function activate()
    {
        $this->_insert_gift_card_designs_as_attachment();
    }

    /**
     * Execute Product_Type class.
     *
     * @since 1.0
     * @access public
     * @inherit AGCFW\Interfaces\Model_Interface
     */
    public function run()
    {
        add_filter('product_type_selector', array($this, 'register_product_type_option'));
        add_filter('woocommerce_product_data_tabs', array($this, 'override_product_data_tabs'));
        add_filter('woocommerce_product_class', array($this, 'register_product_class'), 10, 2);
        add_filter('woocommerce_rest_pre_insert_product_object', array($this, 'override_product_type_in_wc_rest_api_pre_insert'), 10, 2);
        add_action('woocommerce_product_options_general_product_data', array($this, 'display_gift_card_fields'));
        add_action('woocommerce_admin_process_product_object', array($this, 'save_gift_card_fields'));
        add_action('woocommerce_advanced_gift_card_add_to_cart', array($this, 'display_single_product_form'));
        add_filter('woocommerce_order_item_display_meta_key', array($this, 'format_order_item_display_meta_keys'), 10, 2);
        add_filter('woocommerce_order_item_display_meta_value', array($this, 'format_order_item_display_meta_values'), 10, 2);
        add_filter('woocommerce_order_item_get_formatted_meta_data', array($this, 'append_gift_card_expire_value_on_order_item_meta'), 10, 2);
        add_filter('woocommerce_placeholder_img_src', array($this, 'set_gift_card_image_as_product_placeholder_image'));
        add_filter('woocommerce_single_product_summary', array($this, 'display_gift_card_value_template'), 11);
    }

}
