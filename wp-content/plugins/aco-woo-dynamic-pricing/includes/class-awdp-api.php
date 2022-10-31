<?php

if (!defined('ABSPATH'))
    exit;

class AWDP_Api
{

    /**
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;
    private $_active = false;

    public function __construct()
    {
        add_action('rest_api_init', function () {
            register_rest_route('awdp/v1', '/rules/', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_rules'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/rules/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_rules'),
                'permission_callback' => array($this, 'get_permission'),
                // 'args' => ['id']
            ));
            register_rest_route('awdp/v1', '/rules/', array(
                'methods' => 'POST',
                'callback' => array($this, 'post_rule'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/statusChange/', array(
                'methods' => 'POST',
                'callback' => array($this, 'status_change'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/delete/', array(
                'methods' => 'POST',
                'callback' => array($this, 'action_delete'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/productlist/', array(
                'methods' => 'GET',
                'callback' => array($this, 'product_list'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/productlist/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'product_list'),
                'permission_callback' => array($this, 'get_permission'),
                // 'args' => ['id']
            ));
            register_rest_route('awdp/v1', '/product_rule/', array(
                'methods' => 'POST',
                'callback' => array($this, 'product_rule'),
                'permission_callback' => array($this, 'get_permission')
            ));
            register_rest_route('awdp/v1', '/awdp_settings/', array(
                'methods' => 'POST',
                'callback' => array($this, 'awdp_settings'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('awdp/v1', '/awdp_settings/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'awdp_settings'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('awdp/v1', '/data/products', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_products'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('awdp/v1', '/productsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'products_search'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('awdp/v1', '/productlistsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'product_list_search'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('awdp/v1', '/taxsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'taxonomy_search'),
                'permission_callback' => array($this, 'get_permission')
            ));

        });
    }

    /**
     *
     * Ensures only one instance of AWDP is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main AWDP instance
     */
    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    }

    function action_delete($data)
    {
        $data = $data->get_params();
        if ($data['id']) {
            $pt = get_post_type($data['id']);

            if ($pt == AWDP_POST_TYPE && wp_delete_post($data['id'], true)) {
                return admin_url('admin.php?page=awdp_admin_ui');
            } else if ($pt == AWDP_PRODUCT_LIST && wp_delete_post($data['id'], true)) {
                return admin_url('admin.php?page=awdp_admin_product_lists');
            }
        }
    }

    function status_change($data)
    {
        $data       = $data->get_params();
        $wdp_status = ( $data['status'] ) ? 1 : 0;
        $id         = $data['id'];

        if ( $id ) {

            update_post_meta($id, 'discount_status', $wdp_status);

            $all_listings = get_posts(array('fields' => 'ids','posts_per_page' => -1, 'post_type' => AWDP_POST_TYPE));
            $result = array();
            $discount_type_name = Array(
                'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
                'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
                'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
                'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
                'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing')
            );
            foreach ($all_listings as $listID) {
                $date1 = get_post_meta($listID, 'discount_start_date', true);
                $date2 = get_post_meta($listID, 'discount_end_date', true);
                if (!isset($date2) || $date2 == ''){
                    $discount_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
                } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                    $discount_schedule = date_format(date_create($date1), 'jS M Y');
                } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                    $discount_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
                } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                    $discount_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
                } else {
                    $discount_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
                }
                $result[] = Array(
                    'discount_id' => $listID,
                    'discount_title' => html_entity_decode ( get_the_title ( $listID ) ),
                    'discount_status' => get_post_meta($listID, 'discount_status', true),
                    'discount_schedule' => $discount_schedule ? $discount_schedule : '',
                    'discount_type' => get_post_meta($listID, 'discount_type', true),
                    'discount_value' => get_post_meta($listID, 'discount_type', true) != 'cart_quantity' ? get_post_meta($listID, 'discount_value', true) : '',
                    'discount_date' => get_the_date('d M Y', $listID),
                    'discount_priority' => get_post_meta($listID, 'discount_priority', true),
                    'discount_type_name' => array_key_exists ( get_post_meta($listID, 'discount_type', true), $discount_type_name ) ? $discount_type_name[get_post_meta($listID, 'discount_type', true)] : ''
                );
            }
            return new WP_REST_Response($result, 200);

        }
    }

    function awdp_settings($data)
    {

        $checkML            = call_user_func ( array ( new AWDP_ML(), 'is_default_lan' ), '' );
        $currentLang        = !$checkML ? call_user_func ( array ( new AWDP_ML(), 'current_language' ), '' ) : '';

        if( ! $data['id'] ) {
            
            $data = $data->get_params();

            $pricing_title              = $data['pricing_title'] ? $data['pricing_title'] : '';
            $pricing_price_label        = $data['pricing_price_label'] ? $data['pricing_price_label'] : '';
            $pricing_quantity_label     = $data['pricing_quantity_label'] ? $data['pricing_quantity_label'] : '';
            $pricing_new_label          = $data['pricing_new_label'] ? $data['pricing_new_label'] : '';
            $default_fee_label          = $data['default_fee_label'] ? $data['default_fee_label'] : '';
            $dismessagestatus           = $data['discount_message_status'] ? $data['discount_message_status'] : 0;
            $message                    = $data['discount_message'] ? $data['discount_message'] : '';
            $tableposition              = $data['tableposition'] ? $data['tableposition'] : '';
            $tablesort                  = $data['tablesort'] ? $data['tablesort'] : '';
            $tablevalue                 = $data['tablevalue'] ? $data['tablevalue'] : '';
            $tablevaluetext             = $data['tablevaluetext'] ? $data['tablevaluetext'] : '';
            $tablevaluetextdisable      = $data['tablevaluetextdisable'] ? $data['tablevaluetextdisable'] : 0;
            $tablefontsize              = $data['tablefontsize'] ? $data['tablefontsize'] : 0;
            $tableborder                = $data['table_border_color'] ? $data['table_border_color'] : '';
            $disdescription             = $data['discount_description'] ? $data['discount_description'] : '';
            $disitemdescription         = $data['discount_item_description'] ? $data['discount_item_description'] : '';

            $hide_coupon_box            = $data['hide_coupon_box'] ? $data['hide_coupon_box'] : '';
            $disable_discount           = $data['disable_discount'] ? $data['disable_discount'] : '';
            // $apply_coupon_discount      = $data['apply_coupon_discount'] ? $data['apply_coupon_discount'] : '';

            $enable_dismessage          = $data['enable_dismessage'] ? $data['enable_dismessage'] : '';
            $dismessage                 = $data['dismessage'] ? $data['dismessage'] : '';
            $dismessage_rule            = $data['dismessage_rule'] ? $data['dismessage_rule'] : '';
            $dismessage_position        = $data['dismessage_position'] ? $data['dismessage_position'] : '';
            $dismessage_fontsize        = $data['dismessage_fontsize'] ? $data['dismessage_fontsize'] : '';
            $dismessage_paddding_lm     = $data['dismessage_paddding_lm'] ? $data['dismessage_paddding_lm'] : '';
            $dismessage_paddding_tp     = $data['dismessage_paddding_tp'] ? $data['dismessage_paddding_tp'] : '';
            $dismessage_radius          = $data['dismessage_radius'] ? $data['dismessage_radius'] : '';
            $dismessage_background      = $data['dismessage_background'] ? $data['dismessage_background'] : '';
            $dismessage_color           = $data['dismessage_color'] ? $data['dismessage_color'] : '';

            //TimeZone
            $wordpress_timezone         = $data['wordpress_timezone'] ? $data['wordpress_timezone'] : '';

            // Lang Settings
            $langSettings               = get_option('awdp_settings_lang_options') ? get_option('awdp_settings_lang_options') : [];

            $disc_desc_config = array(
                'enable_dismessage'         => $enable_dismessage,
                'dismessage'                => $dismessage,
                'dismessage_rule'           => $dismessage_rule,
                'dismessage_position'       => $dismessage_position,
                'dismessage_fontsize'       => $dismessage_fontsize,
                'dismessage_paddding_lm'    => $dismessage_paddding_lm,
                'dismessage_paddding_tp'    => $dismessage_paddding_tp,
                'dismessage_radius'         => $dismessage_radius,
                'dismessage_background'     => $dismessage_background,
                'dismessage_color'          => $dismessage_color,
            ); 

            $time_zone_config = array(
                'wordpress_timezone'        => $wordpress_timezone
            );

            /*
            * WPML Label
            * Version 4.0.5
            */
            if ( $currentLang ) { 

                // if ( $langSettings && !array_key_exists ( $currentLang, $langSettings ) ) { 
                    $langSettings[$currentLang]['pricing_title']                = $pricing_title;
                    $langSettings[$currentLang]['pricing_price_label']          = $pricing_price_label;
                    $langSettings[$currentLang]['pricing_quantity_label']       = $pricing_quantity_label;
                    $langSettings[$currentLang]['pricing_new_label']            = $pricing_new_label;
                    $langSettings[$currentLang]['tablevaluetext']               = $tablevaluetext;
                    $langSettings[$currentLang]['discount_description']         = $disdescription;
                    $langSettings[$currentLang]['discount_item_description']    = $disitemdescription;
                // } else if ( $langSettings && array_key_exists ( $currentLang, $langSettings ) ) { 
                //     $langSettings[$currentLang]['pricing_title']                = $pricing_title;
                //     $langSettings[$currentLang]['pricing_price_label']          = $pricing_price_label;
                //     $langSettings[$currentLang]['pricing_quantity_label']       = $pricing_quantity_label;
                //     $langSettings[$currentLang]['pricing_new_label']            = $pricing_new_label;
                //     $langSettings[$currentLang]['tablevaluetext']               = $tablevaluetext;
                //     $langSettings[$currentLang]['discount_description']         = $disdescription;
                //     $langSettings[$currentLang]['discount_item_description']    = $disitemdescription;
                // }

                if ( false === get_option('awdp_settings_lang_options') )
                    add_option('awdp_settings_lang_options', $langSettings, '', 'yes');
                else
                    update_option('awdp_settings_lang_options', $langSettings);

            } 
            /*End*/

            if ( false === get_option('awdp_pc_title') )
                add_option('awdp_pc_title', $pricing_title, '', 'yes');
            else
                update_option('awdp_pc_title', $pricing_title);

            if ( false === get_option('awdp_pc_label') )
                add_option('awdp_pc_label', $pricing_price_label, '', 'yes');
            else
                update_option('awdp_pc_label', $pricing_price_label);

            if ( false === get_option('awdp_qn_label') )
                add_option('awdp_qn_label', $pricing_quantity_label, '', 'yes');
            else
                update_option('awdp_qn_label', $pricing_quantity_label);

            if ( false === get_option('awdp_new_label') )
                add_option('awdp_new_label', $pricing_new_label, '', 'yes');
            else
                update_option('awdp_new_label', $pricing_new_label);

            if ( false === get_option('awdp_fee_label') )
                add_option('awdp_fee_label', $default_fee_label, '', 'yes');
            else
                update_option('awdp_fee_label', $default_fee_label);

            if ( false === get_option('awdp_message_status') )
                add_option('awdp_message_status', $dismessagestatus, '', 'yes');
            else
                update_option('awdp_message_status', $dismessagestatus);

            if ( false === get_option('awdp_discount_message') )
                add_option('awdp_discount_message', $message, '', 'yes');
            else
                update_option('awdp_discount_message', $message);

            if ( false === get_option('tableposition') && false === get_option('awdp_table_position') ) {
                add_option('awdp_table_position', $tableposition, '', 'yes');
            } else if ( false != get_option('tableposition') && false === get_option('awdp_table_position') ) {
                add_option('awdp_table_position', $tableposition, '', 'yes');
                update_option('tableposition', $tableposition);
            } else {
                update_option('awdp_table_position', $tableposition);
            }
            
            if ( false === get_option('awdp_table_sort') )
                add_option('awdp_table_sort', $tablesort, '', 'yes');
            else
                update_option('awdp_table_sort', $tablesort);

            if ( false === get_option('awdp_table_value') )
                add_option('awdp_table_value', $tablevalue, '', 'yes');
            else
                update_option('awdp_table_value', $tablevalue);

            if ( false === get_option('awdp_table_value_text') )
                add_option('awdp_table_value_text', $tablevaluetext, '', 'yes');
            else
                update_option('awdp_table_value_text', $tablevaluetext);

            if ( false === get_option('awdp_table_value_notext') )
                add_option('awdp_table_value_notext', $tablevaluetextdisable, '', 'yes');
            else
                update_option('awdp_table_value_notext', $tablevaluetextdisable);

            if ( false === get_option('awdp_tablefontsize') )
                add_option('awdp_tablefontsize', $tablefontsize, '', 'yes');
            else
                update_option('awdp_tablefontsize', $tablefontsize);

            if ( false === get_option('awdp_table_border') )
                add_option('awdp_table_border', $tableborder, '', 'yes');
            else
                update_option('awdp_table_border', $tableborder);
                
            if ( false === get_option('awdp_discount_description') )
                add_option('awdp_discount_description', $disdescription, '', 'yes');
            else
                update_option('awdp_discount_description', $disdescription);

            if ( false === get_option('awdp_discount_item_description') )
                add_option('awdp_discount_item_description', $disitemdescription, '', 'yes');
            else
                update_option('awdp_discount_item_description', $disitemdescription);

            if ( false === get_option('awdp_hide_coupon_box') )
                add_option('awdp_hide_coupon_box', $hide_coupon_box, '', 'yes' );
            else
                update_option('awdp_hide_coupon_box', $hide_coupon_box );

            if ( false === get_option('awdp_disable_discount') )
                add_option('awdp_disable_discount', $disable_discount, '', 'yes' );
            else
                update_option('awdp_disable_discount', $disable_discount );

            if ( false === get_option('awdp_disc_desc_config') )
                add_option('awdp_disc_desc_config', $disc_desc_config, '', 'yes');
            else
                update_option('awdp_disc_desc_config', $disc_desc_config);

            if ( false === get_option('awdp_time_zone_config') )
                add_option('awdp_time_zone_config', $time_zone_config, '', 'yes');
            else
                update_option('awdp_time_zone_config', $time_zone_config);

            // if ( false === get_option('awdp_apply_coupon_discount') )
            //     add_option('awdp_apply_coupon_discount', $apply_coupon_discount, '', 'yes' );
            // else
            //     update_option('awdp_apply_coupon_discount', $apply_coupon_discount );

        }

        $langSettings               = get_option('awdp_settings_lang_options') ? get_option('awdp_settings_lang_options') : [];
        if ( !empty ($langSettings) && array_key_exists ( $currentLang, $langSettings ) ) {
            $pricing_title              = array_key_exists ( 'pricing_title', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_title'] : get_option('awdp_pc_title');
            $pricing_price_label        = array_key_exists ( 'pricing_price_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_price_label'] : get_option('awdp_pc_label');
            $pricing_quantity_label     = array_key_exists ( 'pricing_quantity_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_quantity_label'] : get_option('awdp_qn_label');
            $pricing_new_label          = array_key_exists ( 'pricing_new_label', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['pricing_new_label'] : get_option('awdp_new_label');
            $tablevaluetext             = array_key_exists ( 'tablevaluetext', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['tablevaluetext'] : get_option('awdp_table_value_text');
            $discount_description       = array_key_exists ( 'discount_description', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['discount_description'] : get_option('awdp_discount_description');
            $discount_item_description  = array_key_exists ( 'discount_item_description', $langSettings[$currentLang] ) ? $langSettings[$currentLang]['discount_item_description'] : get_option('awdp_discount_item_description');  
        } else  {
            $pricing_title              = get_option('awdp_pc_title') ? get_option('awdp_pc_title') : '';
            $pricing_price_label        = get_option('awdp_pc_label') ? get_option('awdp_pc_label') : '';
            $pricing_quantity_label     = get_option('awdp_qn_label') ? get_option('awdp_qn_label') : '';
            $pricing_new_label          = get_option('awdp_new_label') ? get_option('awdp_new_label') : '';
            $tablevaluetext             = get_option('awdp_table_value_text') ? get_option('awdp_table_value_text') : '';
            $discount_description       = get_option('awdp_discount_description') ? get_option('awdp_discount_description') : '';
            $discount_item_description  = get_option('awdp_discount_item_description') ? get_option('awdp_discount_item_description') : '';
        }

        $result['pricing_title']                = $pricing_title;
        $result['pricing_price_label']          = $pricing_price_label;
        $result['pricing_quantity_label']       = $pricing_quantity_label;
        $result['pricing_new_label']            = $pricing_new_label;
        $result['tablevaluetext']               = $tablevaluetext;
        $result['discount_description']         = $discount_description;
        $result['discount_item_description']    = $discount_item_description;
        $result['default_fee_label']            = get_option('awdp_fee_label') ? get_option('awdp_fee_label') : '';
        $result['discount_message_status']      = get_option('awdp_message_status') ? get_option('awdp_message_status') : '';
        $result['discount_message']             = get_option('awdp_discount_message') ? get_option('awdp_discount_message') : '';
        $result['tableposition']                = get_option('awdp_table_position') ? get_option('awdp_table_position') : ( get_option('tableposition') ? get_option('tableposition') : '' );
        $result['tablesort']                    = get_option('awdp_table_sort') ? get_option('awdp_table_sort') : '';
        $result['tablevalue']                   = get_option('awdp_table_value') ? get_option('awdp_table_value') : '';
        $result['tablevaluetextdisable']        = get_option('awdp_table_value_notext') ? get_option('awdp_table_value_notext') : '';
        $result['tablefontsize']                = get_option('awdp_tablefontsize') ? get_option('awdp_tablefontsize') : '';
        $result['tableborder']                  = get_option('awdp_table_border') ? get_option('awdp_table_border') : '';

        $result['hide_coupon_box']              = get_option('awdp_hide_coupon_box') ? get_option('awdp_hide_coupon_box') : '';
        $result['disable_discount']             = get_option('awdp_disable_discount') ? get_option('awdp_disable_discount') : '';
        // $result['apply_coupon_discount']        = get_option('awdp_apply_coupon_discount') ? get_option('awdp_apply_coupon_discount') : '';

        $disc_desc_config_saved                 = get_option('awdp_disc_desc_config') ? get_option('awdp_disc_desc_config') : []; 
        $result['enable_dismessage']            = array_key_exists ( 'enable_dismessage', $disc_desc_config_saved ) ? $disc_desc_config_saved['enable_dismessage'] : '';
        $result['dismessage']                   = array_key_exists ( 'dismessage', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage'] : '';
        $result['dismessage_rule']              = array_key_exists ( 'dismessage_rule', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage_rule'] : '';
        $result['dismessage_position']          = array_key_exists ( 'dismessage_position', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage_position'] : '';
        $result['dismessage_fontsize']          = array_key_exists ( 'dismessage_fontsize', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage_fontsize'] : '';
        $result['dismessage_paddding_lm']       = array_key_exists ( 'dismessage_paddding_lm', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage_paddding_lm'] : '';
        $result['dismessage_paddding_tp']       = array_key_exists ( 'dismessage_paddding_tp', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage_paddding_tp'] : '';
        $result['dismessage_radius']            = array_key_exists ( 'dismessage_radius', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage_radius'] : '';
        $result['dismessage_background']        = array_key_exists ( 'dismessage_background', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage_background'] : '';
        $result['dismessage_color']             = array_key_exists ( 'dismessage_color', $disc_desc_config_saved ) ? $disc_desc_config_saved['dismessage_color'] : '';

        $time_zone_config_saved                 = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
        $result['wordpress_timezone']           = array_key_exists ( 'wordpress_timezone', $time_zone_config_saved ) ? $time_zone_config_saved['wordpress_timezone'] : '';

        return new WP_REST_Response($result, 200);
    }

    function post_rule($data)
    {
        $this->delete_transient();
        $data = $data->get_params();
        if ($data['id']) {
            $my_post = array(
                'ID'            => $data['id'],
                'post_title'    => wp_strip_all_tags($data['name']),
                'post_content'  => '',
            );
            wp_update_post($my_post);
            $this->rule_update_meta($data, $data['id']); 
            return $data['id'];
            
        } else {
            $my_post = array(
                'post_type'     => AWDP_POST_TYPE,
                'post_title'    => wp_strip_all_tags($data['name']),
                'post_content'  => '',
                'post_status'   => 'publish',
            );
            $id = wp_insert_post($my_post);
            $this->rule_update_meta($data, $id);
            return $id;
        }
    }

    public function delete_transient()
    {
        delete_transient(AWDP_PRODUCTS_TRANSIENT_KEY);
    }

    function rule_update_meta($data, $id)
    {

        $wdp_start_date     = isset($data['start_date']) ? $data['start_date'] : '';
        $wdp_end_date       = isset($data['end_date']) ? $data['end_date'] : '';
        $wdp_discount_type  = isset($data['discount_type']) ? $data['discount_type'] : '';
        $wdp_discount       = isset($data['discount']) ? $data['discount'] : '';
        $wdp_status         = isset($data['status']) ? $data['status'] : '';
        $wdp_reg_customers  = isset($data['reg_customers']) ? $data['reg_customers'] : '';
        $wdp_custom_pl      = isset($data['custom_pl']) ? $data['custom_pl'] : '';
        $wdp_pricing_table  = isset($data['pricing_table']) ? $data['pricing_table'] : '';
        $wdp_priority       = isset($data['priority']) ? $data['priority'] : '';
        $wdp_product_list   = isset($data['product_list']) ? $data['product_list'] : '';
        $wdp_inc_tax        = isset($data['inc_tax']) ? $data['inc_tax'] : '';
        $wdp_label          = isset($data['label']) ? $data['label'] : '';
        $wdp_sequentially   = isset($data['sequentially']) ? $data['sequentially'] : '';
        $wdp_usage_limit    = isset($data['usage_limit']) ? $data['usage_limit'] : '';
        $wdp_disable_on_sale = isset($data['disable_on_sale']) ? $data['disable_on_sale'] : '';
        $wdp_apply_rule_once = isset($data['apply_rule_once']) ? $data['apply_rule_once'] : '';
        // $wdp_disable_on_rules = isset($data['disable_on_rules']) ? $data['disable_on_rules'] : '';
        $wdp_show_in_loop   = isset($data['show_in_loop']) ? $data['show_in_loop'] : '';
        $wdp_rules          = isset($data['rules']) ? $data['rules'] : '';
        $wdp_quantity_type  = isset($data['quantity_type']) ? $data['quantity_type'] : '';
        $wdp_weekday        = isset($data['discount_schedule_weekday']) ? $data['discount_schedule_weekday'] : '';

        $start_time         = isset($data['startTime']) ? date('H:i', strtotime($data['startTime'] )) : '';
        $end_time           = isset($data['endTime']) ? date('H:i', strtotime($data['endTime'] )) : '';
        
        $table_layout       = isset($data['table_layout']) ? $data['table_layout'] : '';

        $disc_calc_type     = isset($data['disc_calc_type']) ? $data['disc_calc_type'] : '';

        $discount_schedule_days = isset($data['discount_schedule_days']) ? serialize($data['discount_schedule_days']) : '';

        // Dynamic Value
        $dynamic_value  = isset($data['dynamic_value']) ? $data['dynamic_value'] : ''; 

        $customPL       = isset($data['customPL']) ? $data['customPL'] : ''; 

        $schedules      = isset($data['schedules']) ? $data['schedules'] : '';
        $schedule_array = [];
        $key = 0;
        foreach($schedules as $schedule){ 
            // Start Date
            if($schedule['start_date']){
                $start_date = $schedule['start_date'];
                $start_date = date("Y-m-d H:i:s", strtotime($start_date));
                if( ( strtotime(get_post_meta($id, 'discount_start_date', true)) > strtotime($start_date) ) || $key == 0 ) {
                    update_post_meta($id, 'discount_start_date', $start_date);
                } 
            } else {
                $start_date = '';
            }
            // End Date
            if($schedule['end_date']){
                $end_date = $schedule['end_date'];
                $end_date = date("Y-m-d H:i:s", strtotime($end_date));
                if( ( strtotime(get_post_meta($id, 'discount_end_date', true)) < strtotime($end_date) ) || $key == 0 ) {
                    update_post_meta($id, 'discount_end_date', $end_date);
                } 
            } else {
                update_post_meta($id, 'discount_end_date', '');
                $end_date = '';
            }
            $schedule_array[$key]['start_date'] = $start_date;
            $schedule_array[$key]['end_date'] = $end_date;
            $key++;
        }

        $serialize_data     = array_values($schedule_array);
        $schedule_serialize = serialize($serialize_data);
        $quantityranges     = isset($data['quantityranges']) ? serialize($data['quantityranges']) : '';
        $variation_check    = isset($data['quantity_variation_check']) ? $data['quantity_variation_check'] : '';
        $cartamount         = isset($data['cartamount']) ? serialize($data['cartamount']) : '';

        update_post_meta($id, 'discount_schedules', $schedule_serialize);
        update_post_meta($id, 'discount_quantityranges', $quantityranges);
        update_post_meta($id, 'discount_variation_check', $variation_check);
        update_post_meta($id, 'discount_cartamount', $cartamount);
        update_post_meta($id, 'discount_start_time', $start_time);
        update_post_meta($id, 'discount_end_time', $end_time);
        update_post_meta($id, 'discount_schedule_days', $discount_schedule_days);
        update_post_meta($id, 'discount_quantity_type', $wdp_quantity_type);
        update_post_meta($id, 'discount_schedule_weekday', $wdp_weekday);
        update_post_meta($id, 'discount_table_layout', $table_layout);
        update_post_meta($id, 'discount_calc_type', $disc_calc_type);

        update_post_meta($id, 'discount_type', $wdp_discount_type);
        update_post_meta($id, 'discount_value', $wdp_discount);
        update_post_meta($id, 'discount_status', $wdp_status);
        update_post_meta($id, 'discount_reg_customers', $wdp_reg_customers);
        update_post_meta($id, 'discount_custom_pl', $wdp_custom_pl);
        update_post_meta($id, 'discount_pricing_table', $wdp_pricing_table);
        update_post_meta($id, 'discount_priority', $wdp_priority);
        update_post_meta($id, 'discount_product_list', $wdp_product_list);

        $other_config = array(
            'inc_tax' => $wdp_inc_tax,
            'label' => $wdp_label,
            'sequentially' => $wdp_sequentially,
            'usage_limit' => $wdp_usage_limit,
            'disable_on_sale' => $wdp_disable_on_sale,
            'show_in_loop' => $wdp_show_in_loop,
            'rules' => base64_encode(serialize($wdp_rules)),
            'apply_rule_once' => $wdp_apply_rule_once,
            // 'disable_on_rules' => $wdp_disable_on_rules,
        );

        update_post_meta($id, 'discount_config', $other_config);

        update_post_meta($id, 'dynamic_value', $dynamic_value);        
        update_post_meta($id, 'custom_product_list', $customPL);

    }

    function get_rules($data)
    {

        if (isset($data['id'])) {
            $result             = array();
            $discount_rule      = get_post($data['id']);
            $discount_config    = get_post_meta($discount_rule->ID, 'discount_config', true) ? get_post_meta($discount_rule->ID, 'discount_config', true) : [];

            // Scheduling dates
            if(get_post_meta($discount_rule->ID, 'discount_schedules', true)){
                $schedules = unserialize(get_post_meta($discount_rule->ID, 'discount_schedules', true));
            } else if(get_post_meta($discount_rule->ID, 'discount_start_date', true) && get_post_meta($discount_rule->ID, 'discount_end_date', true)){ // data before scheduling
                $schedules[0]['start_date'] = get_post_meta($discount_rule->ID, 'discount_start_date', true);
                $schedules[0]['end_date'] = get_post_meta($discount_rule->ID, 'discount_end_date', true);
            }

            $PListID = (int)get_post_meta($discount_rule->ID, 'discount_product_list', true);
            $select_array[] = array ( 'label' => 'Any Product', 'value' => '' ); 
            $select_array[] = array ( 'label' => html_entity_decode ( get_the_title ( $PListID ) ), 'value' => $PListID ); 

            /* 
            * Wordpress Time Zone Settings
            * @ Ver 4.0.8
            */
            $wp_tz_stngs    = get_option('awdp_time_zone_config') ? get_option('awdp_time_zone_config') : []; 
            $wp_tz          = array_key_exists ( 'wordpress_timezone', $wp_tz_stngs ) ? $wp_tz_stngs['wordpress_timezone'] : '';
            if ( $wp_tz ) {
                $timezone = new DateTimeZone( wp_timezone_string() );
                $schd_time = wp_date("F d, Y H:i", null, $timezone );
            } else {
                $schd_time = gmdate('F d, Y H:i');
            }
            /**/

            $customPL           = get_post_meta($discount_rule->ID, 'custom_product_list', true) ? get_post_meta($discount_rule->ID, 'custom_product_list', true) : [];
            $defaultTax         = [];
            $defaultProducts    = [];

            if ( !empty ( $customPL ) ) {
                global $wpdb; $taxvalues = $prodvalues = ''; $tx_cnt = $pr_cnt = 1;
                foreach ( $customPL as $singlePL ) { 
                    foreach ( $singlePL['rules'] as $val ) { 
                        if ( is_array ( $val ) && $val['rule']['value'] ) {
                            if ( $val['rule']['item'] == 'product_selection') {
                                if ( $pr_cnt != 1 ) $prodvalues .= ',';
                                $prodvalues .= implode ( ',', $val['rule']['value'] ); 
                                $pr_cnt++;
                            } else {
                                if ( $tx_cnt != 1 ) $taxvalues .= ',';
                                $taxvalues .= implode ( ',', $val['rule']['value'] ); 
                                $tx_cnt++;
                            }
                        }
                    } 
                    if( $taxvalues != '' ) { 
                        $defaultTax = $wpdb->get_results ( "SELECT DISTINCT cat.term_id as value, cat.name as label FROM {$wpdb->prefix}terms cat LEFT JOIN {$wpdb->prefix}term_taxonomy cattax ON cat.term_id = cattax.term_id WHERE cattax.term_id IN (" . $taxvalues . ")" ); 
                    } 
                    if( $prodvalues != '' ) { 
                        $defaultProducts = $wpdb->get_results ( "SELECT DISTINCT ID as value, post_title as label FROM {$wpdb->prefix}posts WHERE ID IN (" . $prodvalues . ")" ); 
                    } 
                }
            }

            $result = Array(
                'name'                  => $discount_rule->post_title,
                'id'                    => $discount_rule->ID,
                'status'                => get_post_meta($discount_rule->ID, 'discount_status', true),
                'pricing_table'         => get_post_meta($discount_rule->ID, 'discount_pricing_table', true),
                'discount_type'         => get_post_meta($discount_rule->ID, 'discount_type', true),
                'reg_customers'         => get_post_meta($discount_rule->ID, 'discount_reg_customers', true),

                'custom_pl'             => get_post_meta($discount_rule->ID, 'discount_custom_pl', true) ? get_post_meta($discount_rule->ID, 'discount_custom_pl', true) : 0,

                'server_date_time'      => $schd_time,
                'start_date'            => get_post_meta($discount_rule->ID, 'discount_start_date', true),
                'end_date'              => get_post_meta($discount_rule->ID, 'discount_end_date', true),
                'start_time'            => date('Y-m-d H:i:s', strtotime(get_post_meta($discount_rule->ID, 'discount_start_time', true))),
                'end_time'              => date('Y-m-d H:i:s', strtotime(get_post_meta($discount_rule->ID, 'discount_end_time', true))),
                'discount_schedule_days' => unserialize(get_post_meta($discount_rule->ID, 'discount_schedule_days', true)),

                'product_list'          => $PListID,
                'select_productlist'    => $select_array,
                'priority'              => get_post_meta($discount_rule->ID, 'discount_priority', true),
                'discount'              => get_post_meta($discount_rule->ID, 'discount_value', true),
                'table_layout'          => get_post_meta($discount_rule->ID, 'discount_table_layout', true),
                'disc_calc_type'        => get_post_meta($discount_rule->ID, 'discount_calc_type', true),
                
                'quantityranges'        => unserialize(get_post_meta($discount_rule->ID, 'discount_quantityranges', true)),
                'quantity_variation_check' => get_post_meta($discount_rule->ID, 'discount_variation_check', true),
                'cartamount'            => unserialize(get_post_meta($discount_rule->ID, 'discount_cartamount', true)),
                'quantity_type'         => get_post_meta($discount_rule->ID, 'discount_quantity_type', true),
                'schedule_weekday'      => get_post_meta($discount_rule->ID, 'discount_schedule_weekday', true),
                'schedules'             => $schedules,

                'disable_on_sale'       => array_key_exists ( 'disable_on_sale', $discount_config ) ? $discount_config['disable_on_sale'] : '',
                'apply_rule_once'       => array_key_exists ( 'apply_rule_once', $discount_config ) ? $discount_config['apply_rule_once'] : '',
                // 'disable_on_rules' => $discount_config['disable_on_rules'],
                'inc_tax'               => array_key_exists ( 'inc_tax', $discount_config ) ? $discount_config['inc_tax'] : '',
                'label'                 => array_key_exists ( 'label', $discount_config ) ? $discount_config['label'] : '',

                'usage_limit'           => array_key_exists ( 'usage_limit', $discount_config ) ? $discount_config['usage_limit'] : '',
                'sequentially'          => array_key_exists ( 'sequentially', $discount_config ) ? $discount_config['sequentially'] : '',
                'show_in_loop'          => array_key_exists ( 'show_in_loop', $discount_config ) ? $discount_config['show_in_loop'] : '',
                'rules'                 => array_key_exists ( 'rules', $discount_config ) ? array_values(array_filter(unserialize(base64_decode($discount_config['rules'])))) : '', // remove empty values

                'dynamic_value'         => get_post_meta($discount_rule->ID, 'dynamic_value', true),
                
                'listUrl'               => admin_url('admin.php?page=awdp_admin_product_lists#/'),
                
                'customPL'              => $customPL,

                'defaultTax'            => $defaultTax,
                'defaultProducts'       => $defaultProducts
            );
            return new WP_REST_Response($result, 200);
        }

        $all_listings = get_posts(array('fields' => 'ids','posts_per_page' => -1, 'post_type' => AWDP_POST_TYPE));
        $result = array();
        $discount_type_name = Array(
            'percent_total_amount'  => __('Percentage of cart total amount', 'aco-woo-dynamic-pricing'),
            'percent_product_price' => __('Percentage of product price', 'aco-woo-dynamic-pricing'),
            'fixed_product_price'   => __('Fixed price of product price', 'aco-woo-dynamic-pricing'),
            'fixed_cart_amount'     => __('Fixed price of cart total amount', 'aco-woo-dynamic-pricing'),
            'cart_quantity'         => __('Quantity based discount', 'aco-woo-dynamic-pricing')
        );
        foreach ($all_listings as $listID) {
            $date1 = get_post_meta($listID, 'discount_start_date', true);
            $date2 = get_post_meta($listID, 'discount_end_date', true);
            if (!isset($date2) || $date2 == ''){
                $discount_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                $discount_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                $discount_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else {
                $discount_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
            }
            $result[] = Array(
                'discount_id'       => $listID,
                'discount_title'    => html_entity_decode ( get_the_title ( $listID ) ),
                'discount_status'   => get_post_meta($listID, 'discount_status', true),
                'discount_schedule' => $discount_schedule ? $discount_schedule : '',
                'discount_type'     => get_post_meta($listID, 'discount_type', true),
                'discount_value'    => get_post_meta($listID, 'discount_type', true) != 'cart_quantity' ? get_post_meta($listID, 'discount_value', true) : '',
                'discount_date'     => get_the_date('d M Y', $listID),
                'discount_priority' => get_post_meta($listID, 'discount_priority', true),
                'discount_type_name' => array_key_exists ( get_post_meta($listID, 'discount_type', true), $discount_type_name ) ? $discount_type_name[get_post_meta($listID, 'discount_type', true)] : ''
            );
        }
        return new WP_REST_Response($result, 200);
    }

    function product_list($data)
    {
        if (isset($data['id'])) {
            global $wpdb;
            $result                 = array();
            $list_item              = get_post($data['id']);
            $result['list_name']    = $list_item->post_title;
            $result['list_id']      = $list_item->ID;
            $result['list_type']    = get_post_meta($list_item->ID, 'list_type', true);
            $other_config           = get_post_meta($list_item->ID, 'product_list_config', true);

            $rules  = $other_config['rules']; 
            $tax    = []; $values = ''; $ar_cnt = 1;
            if($rules) {
                foreach ( $rules as $rule ) { 
                    foreach ( $rule['rules'] as $val ) { 
                        if ( is_array ( $val ) && $val['rule']['value'] ) {
                            if ( $ar_cnt != 1 ) $values .= ',';
				            $values .= implode ( ',', $val['rule']['value'] ); 
                        }
                        $ar_cnt++;
                    } 
                    if( $values != '' ) { 
                        $tax = $wpdb->get_results ( "SELECT DISTINCT cat.term_id as value, cat.name as label FROM {$wpdb->prefix}terms cat LEFT JOIN {$wpdb->prefix}term_taxonomy cattax ON cat.term_id = cattax.term_id WHERE cattax.term_id IN (" . $values . ")" ); 
                    }
                }

                foreach ( $other_config['rules'] as $key => $val) { 
                    $other_config['rules'][$key]['rules'] =  array_values(array_filter($other_config['rules'][$key]['rules'])); 
                }
            }

            $result['selectedProducts'] = array_key_exists('selectedProducts', $other_config) ? ($other_config['selectedProducts']) : '';
            $result['productAuthor']    = array_key_exists('productAuthor', $other_config) ? ($other_config['productAuthor']) : '';
            $result['excludedProducts'] = array_key_exists('excludedProducts', $other_config) ? ($other_config['excludedProducts']) : '';
            $result['taxRelation']      = array_key_exists('taxRelation', $other_config) ? ($other_config['taxRelation']) : '';
            $result['selectionMethod']  = ( array_key_exists('selectionMethod', $other_config) && $other_config['selectionMethod'] ) ? $other_config['selectionMethod'] : 'productname';
            $result['rules']            = array_key_exists('rules', $other_config) ? ($other_config['rules']) : '';
            $defaultProducts            = array_merge(is_array($result['excludedProducts']) ? $result['excludedProducts'] : [], is_array($result['selectedProducts']) ? $result['selectedProducts'] : []); 
            $result['defaultProducts']  = empty($defaultProducts) ? [] : $this->get_products($defaultProducts);  // used for product list suggestion dropdown

            $result['defaultTax'] = $tax;

            return new WP_REST_Response($result, 200);
        }

        $all_listings = get_posts ( array ( 'fields' => 'ids', 'numberposts' => -1, 'post_type' => AWDP_PRODUCT_LIST ) );
        $result = array();
        foreach ($all_listings as $listID) {
            $typ = '';
            if ( get_post_meta($listID, 'list_type', true) == 'products_selection' ) {
                $typ = 'Product Selection';
            } else if ( get_post_meta($listID, 'list_type', true) == 'dynamic_request' ) {
                $typ = 'Dynamic Request';
            }
            $result[] = array(
                'list_id'   => $listID,
                'list_name' => get_the_title($listID) ? html_entity_decode ( get_the_title ( $listID ) ) : 'No Label',
                'list_type' => $typ,
                'list_date' => get_the_date('d M Y', $listID)
            );
        }
        return new WP_REST_Response($result, 200);
    }

    /**
     *
     */
    public function get_products($arg)
    {

        /*
        * version 4.0.7
        * Removed HTML Entities from Titles - html_entity_decode 
        */
        if (is_a($arg, 'WP_REST_Request')) {

            $productslist = get_posts(array('fields' => 'ids','numberposts' => -1, 'post_type' => 'product'));
            $products = Array();
            foreach ($productslist as $product) {
                if(  empty($products) || array_search ( $product, array_column ( $products, 'value' ) ) === false ) {
                    $products[] = [
                        'value' => $product,
                        'label' => html_entity_decode ( get_the_title ( $product ) )
                    ];
                }
            }
            return new WP_REST_Response($products, 200);

        } else {

            $productslist = $arg;
            $products = [];
            foreach ($productslist as $product) { 
                if( empty($products) || array_search ( $product, array_column ( $products, 'value' ) ) === false ) { 
                    $products[] = [
                        'value' => $product,
                        'label' => html_entity_decode ( get_the_title ( $product ) )
                    ];
                }
            }
            return $products;

        }
        
    }

    function product_rule($data)
    {
        $data = $data->get_params();
        $this->delete_transient();
        if ($data['id']) {
            $my_post = array(
                'ID'            => $data['id'],
                'post_title'    => $data['name'] ? wp_strip_all_tags($data['name']) : 'Product List',
                'post_content'  => '',
            );
            wp_update_post($my_post);
            $this->update_product_rule_meta($data['id'], $data);
            return $data['id'];
        } else {
            $my_post = array(
                'post_type'     => AWDP_PRODUCT_LIST,
                'post_title'    => $data['name'] ? wp_strip_all_tags($data['name']) : 'Product List',
                'post_content'  => '',
                'post_status'   => 'publish',
            );
            $id = wp_insert_post($my_post);
            $this->update_product_rule_meta($id, $data);
            return $id;
        }
    }

    function update_product_rule_meta($id, $data)
    {

        update_post_meta($id, 'list_type', $data['list_type']);
        $other_config = array(
            'selectedProducts'  => ($data['selectedProducts']),
            'productAuthor'     => ($data['productAuthor']),
            'excludedProducts'  => ($data['excludedProducts']),
            'taxRelation'       => ($data['taxRelation']),
            'rules'             => ($data['rules']),
            'selectionMethod'   => ($data['selectionMethod']),
        );
        update_post_meta($id, 'product_list_config', $other_config);

    }

    
    /**
     * @search parameter - title
     */
    public function product_list_search($arg)
    {
        global $wpdb;
        $params = $arg->get_params();
        $search = $params['search'];

        $results = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts WHERE post_type in ( 'awdp_pt_products' ) AND post_status = 'publish' AND ( post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );

        foreach ( $results as $result ) { 
            // $result->value = (int)$result->value; 
            // $result->label = $result->label; 
            if ( $result->label === '' ) $result->label = 'Product List';
            $result->value = 'list_'.$result->value; 
        } 

        return new WP_REST_Response($results, 200);
    }

    /**
     * @search parameter - title
     */
    public function products_search($arg)
    {
        global $wpdb;
        $params = $arg->get_params();
        $search = $params['search'];
        // $method = $params['method'] ? $params['method'] : 'productname';

        // if ( $method == 'productsku' ) {
        //     $results = $wpdb->get_results ( "SELECT {$wpdb->prefix}posts.post_title as label, {$wpdb->prefix}posts.ID as value, {$wpdb->prefix}posts.post_type as type FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id)WHERE {$wpdb->prefix}posts.post_type in 'product' AND {$wpdb->prefix}posts.post_status = 'publish' AND {$wpdb->prefix}postmeta.meta_key = '_sku'AND {$wpdb->prefix}postmeta.meta_value = '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' GROUP BY {$wpdb->prefix}posts.ID, {$wpdb->prefix}posts.post_title" );
        // } else {
            $results = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts WHERE post_type in ( 'product' ) AND post_status = 'publish' AND ( post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );
        // }

        foreach ( $results as $result ) { 
            $result->value = (int)$result->value;
        } 

        return new WP_REST_Response($results, 200);
    }

    
    /**
     * @search parameter - title
     */
    public function taxonomy_search($arg)
    {
        global $wpdb;
        $params = $arg->get_params();
        $search = $params['search'];
        $tax    = ( $params['tax'] == 'tag' ) ? 'product_tag' : 'product_cat';

        $results = $wpdb->get_results ( "SELECT cat.term_id AS value, cat.name AS label FROM {$wpdb->prefix}terms cat LEFT JOIN {$wpdb->prefix}term_taxonomy cattax ON cat.term_id = cattax.term_id WHERE cattax.taxonomy = '" . $tax . "' AND ( cat.name LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR cat.name LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR cat.name LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' )" );

        foreach ( $results as $result ) { 
            $result->value = (int)$result->value;
        } 

        return new WP_REST_Response($results, 200);
    }


    /**
     * Permission Callback
     **/
    public function get_permission()
    {
        if (current_user_can('administrator') || current_user_can('manage_woocommerce')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    }

}
