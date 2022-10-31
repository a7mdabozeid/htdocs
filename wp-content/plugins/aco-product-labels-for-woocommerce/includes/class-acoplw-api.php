<?php

if (!defined('ABSPATH'))
    exit;

class ACOPLW_Api
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

            register_rest_route('acoplw/v1', '/badges/', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_badges'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/badges/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_badges'),
                'permission_callback' => array($this, 'get_permission'),
                // 'args' => ['id']
            ));

            register_rest_route('acoplw/v1', '/badges/', array(
                'methods' => 'POST',
                'callback' => array($this, 'post_badge'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/delete/', array(
                'methods' => 'POST',
                'callback' => array($this, 'action_delete'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/duplicate/', array(
                'methods' => 'POST',
                'callback' => array($this, 'action_duplicate'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/statusChange/', array(
                'methods' => 'POST',
                'callback' => array($this, 'status_change'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/productlist/', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_list'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/productlist/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_list'),
                'permission_callback' => array($this, 'get_permission'),
                // 'args' => ['id']
            ));

            register_rest_route('acoplw/v1', '/productlist/', array(
                'methods' => 'POST',
                'callback' => array($this, 'post_list'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/settings/', array(
                'methods' => 'POST',
                'callback' => array($this, 'pluginSettings'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/settings/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'pluginSettings'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/data/products', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_products'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/productsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'products_search'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/productlistsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'product_list_search'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/taxsearch', array(
                'methods' => 'GET',
                'callback' => array($this, 'taxonomy_search'),
                'permission_callback' => array($this, 'get_permission')
            ));

            register_rest_route('acoplw/v1', '/getpreviewthumb/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_thumb'),
                'permission_callback' => array($this, 'get_permission')
            ));

        });
    }

    /**
     *
     * Ensures only one instance of ACOPLW is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WordPress_Plugin_Template()
     * @return Main ACOPLW instance
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

            if ($pt == ACOPLW_POST_TYPE && wp_delete_post($data['id'], true)) {
                return admin_url('admin.php?page=acoplw_badges_ui');
            } else if ($pt == ACOPLW_PRODUCT_LIST && wp_delete_post($data['id'], true)) {
                return admin_url('admin.php?page=acoplw_product_lists_ui');
            }
        }
    }

    function action_duplicate($data) 
    {
        $data = $data->get_params();
        $id = $data['id']; 
        if ( $id ) {

            $title = get_the_title ( $id );
            $author_id = get_post_field ( 'post_author', $id );
            $badge_args = array (
                'post_title' => $title,
                'post_status' => 'publish',
                'post_type' => ACOPLW_POST_TYPE,
                'post_author' => $author_id
            );
            $newBadgeID = wp_insert_post ( $badge_args );

            // Copy post metadata
            $data = get_post_custom ( $id ); 
            foreach ( $data as $key => $values ) {
                foreach ( $values as $value ) { 
                    if ( $key === 'badge_label_options' || $key === 'badge_style_options' || $key === 'badge_position_options' || $key === 'badge_preview_options' || $key === 'badge_schedules' ) {
                        $value = unserialize ( $value );
                    }
                    if ( ! add_post_meta( $newBadgeID, $key, $value, true ) ) { 
                        update_post_meta ( $newBadgeID, $key, $value );
                    } else {
                        add_post_meta ( $newBadgeID, $key, $value );
                    }
                }
            }

            // Get all badges
            $all_listings = get_posts ( array ( 'fields' => 'ids','posts_per_page' => -1, 'post_type' => ACOPLW_POST_TYPE ) );
            $result = array();

            foreach ($all_listings as $listID) {
                $date1 = get_post_meta($listID, 'badge_start_date', true);
                $date2 = get_post_meta($listID, 'badge_end_date', true);
                $style_options = get_post_meta($listID, 'badge_style_options', true);
                $label_options = get_post_meta($listID, 'badge_label_options', true);

                if (!isset($date2) || $date2 == ''){
                    $badge_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
                } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                    $badge_schedule = date_format(date_create($date1), 'jS M Y');
                } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                    $badge_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
                } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                    $badge_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
                } else {
                    $badge_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
                }
                $result[] = Array(
                    'badgeID'           => $listID,
                    'badgeTitle'        => get_the_title($listID),
                    'badgeStatus'       => get_post_meta($listID, 'badge_status', true),
                    'badgeDate'         => get_the_date('d M Y', $listID),
                    'badgeLabel'        => $label_options['badgeLabel'],
                    'badgeLabelColor'   => $label_options['badgeLabelColor'],
                    'badgeStyle'        => $style_options['badgeStyle'],
                    'badgeColor'        => $style_options['badgeColor']
                );
            }
            return new WP_REST_Response($result, 200);

        }
    }

    function status_change($data)
    {
        $data       = $data->get_params();
        $wdp_status = ( $data['status'] );
        $id         = $data['id'];

        /*
        * Dynamic Labels Text
        * @ ver 1.3.0
        */
        $dynamicLabels  = array ( 'day' => date("l"), 'month' => date("F"), 'year' => date("Y"), 'sale' => '50%' );

        if ($id) { 

            update_post_meta ( $id, 'badge_status', $wdp_status );

            // Get all badges
            $all_listings   = get_posts ( array ( 'fields' => 'ids','posts_per_page' => -1, 'post_type' => ACOPLW_POST_TYPE ) );
            $result         = array();

            foreach ($all_listings as $listID) {
                $date1          = get_post_meta($listID, 'badge_start_date', true);
                $date2          = get_post_meta($listID, 'badge_end_date', true);
                $style_options  = get_post_meta($listID, 'badge_style_options', true);
                $label_options  = get_post_meta($listID, 'badge_label_options', true);

                if (!isset($date2) || $date2 == ''){
                    $badge_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
                } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                    $badge_schedule = date_format(date_create($date1), 'jS M Y');
                } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                    $badge_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
                } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                    $badge_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
                } else {
                    $badge_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
                }
                $result[] = Array(
                    'badgeID'           => $listID,
                    'badgeTitle'        => get_the_title($listID),
                    'badgeStatus'       => get_post_meta($listID, 'badge_status', true),
                    'badgeDate'         => get_the_date('d M Y', $listID),
                    'badgeLabel'        => $label_options['badgeLabel'],
                    'badgeLabelColor'   => $label_options['badgeLabelColor'],
                    'badgeStyle'        => $style_options['badgeStyle'],
                    'badgeColor'        => $style_options['badgeColor'],
                    'dynamicLabels'     => $dynamicLabels
                );
            }
            return new WP_REST_Response($result, 200);

        }
    }

    function pluginSettings($data)
    {
            
        $data = $data->get_params();

        if( ! $data['id'] ) {

            $dplist             = $data['dp_list_status'] ? $data['dp_list_status'] : 0;
            $enable_title_hook  = $data['enable_title_hook'] ? $data['enable_title_hook'] : 0;
            $enable_loop_hook   = $data['enable_loop_hook'] ? $data['enable_loop_hook'] : 0;
            $wcbadge            = $data['hide_wc_badge'] ? $data['hide_wc_badge'] : 0;
            $wrapperclass       = $data['wrapperclass'] ? $data['wrapperclass'] : '';
            $badgeDetail        = $data['enableBadgeDetail'] ? $data['enableBadgeDetail'] : 0;
            $enableJquery       = $data['enableJquery'] ? $data['enableJquery'] : 0;

            if ( false === get_option('acoplw_dp_list_status') )
                add_option('acoplw_dp_list_status', $dplist, '', 'yes');
            else
                update_option('acoplw_dp_list_status', $dplist);

            if ( false === get_option('acoplw_wc_badge_status') )
                add_option('acoplw_wc_badge_status', $wcbadge, '', 'yes');
            else
                update_option('acoplw_wc_badge_status', $wcbadge);

            if ( false === get_option('acoplw_enable_title_hook') )
                add_option('acoplw_enable_title_hook', $enable_title_hook, '', 'yes');
            else
                update_option('acoplw_enable_title_hook', $enable_title_hook);

            if ( false === get_option('acoplw_wrapper_class') )
                add_option('acoplw_wrapper_class', $wrapperclass, '', 'yes');
            else
                update_option('acoplw_wrapper_class', $wrapperclass);

            if ( false === get_option('acoplw_enable_loop_hook') )
                add_option('acoplw_enable_loop_hook', $enable_loop_hook, '', 'yes');
            else
                update_option('acoplw_enable_loop_hook', $enable_loop_hook);

            if ( false === get_option('acoplw_detail_page_badge') )
                add_option('acoplw_detail_page_badge', $badgeDetail, '', 'yes');
            else
                update_option('acoplw_detail_page_badge', $badgeDetail);

            if ( false === get_option('acoplw_jquery_status') )
                add_option('acoplw_jquery_status', $enableJquery, '', 'yes');
            else
                update_option('acoplw_jquery_status', $enableJquery);

        }

        $result['dp_list_status']       = get_option('acoplw_dp_list_status') ? get_option('acoplw_dp_list_status') : 0;
        $result['enable_title_hook']    = get_option('acoplw_enable_title_hook') ? get_option('acoplw_enable_title_hook') : 0;
        $result['hide_wc_badge']        = get_option('acoplw_wc_badge_status') ? get_option('acoplw_wc_badge_status') : 0;
        $result['enable_loop_hook']     = get_option('acoplw_enable_loop_hook') ? get_option('acoplw_enable_loop_hook') : 0;
        $result['enableJquery']         = get_option('acoplw_jquery_status') ? get_option('acoplw_jquery_status') : 0;
        $result['enableBadgeDetail']    = ( false === get_option('acoplw_detail_page_badge') ) ? 1 : ( get_option('acoplw_detail_page_badge') ? get_option('acoplw_detail_page_badge') : 0 );
        $result['dp_plugin_active']     = function_exists('AWDP') ? true : false;
        $result['wrapperclass']         = get_option('acoplw_wrapper_class') ? get_option('acoplw_wrapper_class') : '';

        return new WP_REST_Response($result, 200);
    }

    function post_badge($data)
    {
        $this->delete_transient();
        $data = $data->get_params();

        if ($data['id']) { // Update
            $my_post = array(
                'ID' => $data['id'],
                'post_title' => $data['badgeTitle'] ? wp_strip_all_tags($data['badgeTitle']) : 'Woo Badge',
                'post_content' => '',
            );
            wp_update_post($my_post);
            $this->badge_update_meta($data, $data['id']); 
            return $data['id'];
            
        } else { // Create
            $my_post = array(
                'post_type' => ACOPLW_POST_TYPE,
                'post_title' => $data['badgeTitle'] ? wp_strip_all_tags($data['badgeTitle']) : 'Woo Badge',
                'post_content' => '',
                'post_status' => 'publish',
            );
            $id = wp_insert_post($my_post);
            $this->badge_update_meta($data, $id);
            return $id;
        }
    }

    public function delete_transient()
    {
        delete_transient(ACOPLW_PRODUCTS_TRANSIENT_KEY);
    }

    function badge_update_meta($data, $id)
    {

        $schedule_array = [];
        $key = 0;

        $checkML            = call_user_func ( array ( new ACOPLW_ML(), 'is_default_lan' ), '' );
        $currentLang        = !$checkML ? call_user_func ( array ( new ACOPLW_ML(), 'current_language' ), '' ) : '';
        $label_options      = get_post_meta( $id, 'badge_label_options', true ) ? get_post_meta( $id, 'badge_label_options', true ) : [];
        $dataBadgeLabel     = isset ( $data['badgeLabel'] ) ? $data['badgeLabel'] : '';
        $langOptions        = array_key_exists ( 'badgeLabelLang', $label_options ) ? $label_options['badgeLabelLang'] : [];

        if ( $currentLang ) {
            if ( $langOptions && !array_key_exists ( $currentLang, $langOptions ) ) { 
                $langOptions[$currentLang] = $dataBadgeLabel;
            } else if ( $langOptions && array_key_exists ( $currentLang, $langOptions ) ) { 
                $langOptions[$currentLang] = $dataBadgeLabel;
            }
        }
        
        $status             = isset ( $data['badgeStatus'] ) ? $data['badgeStatus'] : '';
        $schedules          = isset ( $data['schedules'] ) ? $data['schedules'] : '';
        $productSchedule    = isset ( $data['productSchedule'] ) ? $data['productSchedule'] : ''; 
        $mainLabel          = !$checkML ? ( array_key_exists ( 'badgeLabel', $label_options ) ? $label_options['badgeLabel'] : '' ) : $dataBadgeLabel; 

        $badgeLabel = array(
            'badgeLabel'            => $mainLabel,
            'badgeLabelColor'       => isset ( $data['badgeLabelColor'] ) ? $data['badgeLabelColor'] : '',
            'badgeLabelColorHex'    => isset ( $data['badgeLabelColorHex'] ) ? $data['badgeLabelColorHex'] : '',
            'fontSize'              => isset ( $data['fontSize'] ) ? $data['fontSize'] : '',
            'lineHeight'            => isset ( $data['lineHeight'] ) ? $data['lineHeight'] : '',
            'badgeLabelLang'        => $langOptions
        );

        $badgeStyle = array(
            'badgeStyle'            => isset ( $data['badgeStyle'] ) ? $data['badgeStyle'] : '',
            'badgeColor'            => isset ( $data['badgeColor'] ) ? $data['badgeColor'] : '',
            'badgeColorHex'         => isset ( $data['badgeColorHex'] ) ? $data['badgeColorHex'] : '',
            'badgeWidth'            => isset ( $data['badgeWidth'] ) ? $data['badgeWidth'] : '',
            'badgeHeight'           => isset ( $data['badgeHeight'] ) ? $data['badgeHeight'] : '',
            'borderTopLeft'         => isset ( $data['borderTopLeft'] ) ? $data['borderTopLeft'] : '',
            'borderTopRight'        => isset ( $data['borderTopRight'] ) ? $data['borderTopRight'] : '',
            'borderBottomLeft'      => isset ( $data['borderBottomLeft'] ) ? $data['borderBottomLeft'] : '',
            'borderBottomRight'     => isset ( $data['borderBottomRight'] ) ? $data['borderBottomRight'] : '',
            'zIndex'                => isset ( $data['zIndex'] ) ? $data['zIndex'] : '',
        );

        $badgePosition = array(
            'opacity'                   => isset ( $data['opacity'] ) ? $data['opacity'] : '',
            'rotationX'                 => isset ( $data['rotationX'] ) ? $data['rotationX'] : '',
            'rotationY'                 => isset ( $data['rotationY'] ) ? $data['rotationY'] : '',
            'rotationZ'                 => isset ( $data['rotationZ'] ) ? $data['rotationZ'] : '',
            'flipHorizontal'            => isset ( $data['flipHorizontal'] ) ? $data['flipHorizontal'] : '',
            'flipVertical'              => isset ( $data['flipVertical'] ) ? $data['flipVertical'] : '',
            'badgePosition'             => isset ( $data['badgePosition'] ) ? $data['badgePosition'] : '',
            'badgePositionHorizontal'   => isset ( $data['badgePositionHorizontal'] ) ? $data['badgePositionHorizontal'] : '',
            'posTop'                    => isset ( $data['posTop'] ) ? $data['posTop'] : '',
            'posBottom'                 => isset ( $data['posBottom'] ) ? $data['posBottom'] : '',
            'posLeft'                   => isset ( $data['posLeft'] ) ? $data['posLeft'] : '',
            'posRight'                  => isset ( $data['posRight'] ) ? $data['posRight'] : '',
        );

        $previewSettings = array(
            'assignAll'             => isset ( $data['assignAll'] ) ? $data['assignAll'] : '',
            'previewSelected'       => isset ( $data['previewSelected'] ) ? $data['previewSelected'] : '',
        );

        $selectedList = isset ( $data['selectedList'] ) ? $data['selectedList'] : '';
        
        foreach($schedules as $schedule){ 
            // Start Date
            if($schedule['start_date']){
                $start_date = $schedule['start_date'];
                $start_date = date("Y-m-d H:i:s", strtotime($start_date));
                if( ( strtotime(get_post_meta($id, 'badge_start_date', true)) > strtotime($start_date) ) || $key == 0 ) {
                    update_post_meta ( $id, 'badge_start_date', $start_date );
                } 
            } else {
                $start_date = '';
            }
            // End Date
            if($schedule['end_date']){
                $end_date = $schedule['end_date'];
                $end_date = date("Y-m-d H:i:s", strtotime($end_date));
                if( ( strtotime(get_post_meta($id, 'badge_end_date', true)) < strtotime($end_date) ) || $key == 0 ) {
                    update_post_meta ( $id, 'badge_end_date', $end_date );
                } 
            } else {
                update_post_meta ( $id, 'badge_end_date', '' );
                $end_date = '';
            }
            $schedule_array[$key]['start_date'] = $start_date;
            $schedule_array[$key]['end_date']   = $end_date;
            $key++;
        }

        $serialize_data     = array_values($schedule_array);
        $schedule_serialize = serialize($serialize_data);

        update_post_meta ( $id, 'badge_status', $status );
        update_post_meta ( $id, 'badge_schedules', $schedule_serialize );
        update_post_meta ( $id, 'badge_label_options', $badgeLabel );
        update_post_meta ( $id, 'badge_style_options', $badgeStyle );
        update_post_meta ( $id, 'badge_position_options', $badgePosition );
        update_post_meta ( $id, 'badge_preview_options', $previewSettings );
        update_post_meta ( $id, 'badge_use_pschedule', $productSchedule );
        update_post_meta ( $id, 'badge_selected_list', $selectedList );

    }

    function get_badges ( $data ) 
    {
        $data               = $data->get_params();
        $checkML            = call_user_func ( array ( new ACOPLW_ML(), 'is_default_lan' ), '' );
        $currentLang        = !$checkML ? call_user_func ( array ( new ACOPLW_ML(), 'current_language' ), '' ) : '';
        
        /*
        * Dynamic Labels Text
        * @ ver 1.3.0
        */
        $dynamicLabels  = array ( 'day' => date("l"), 'month' => date("F"), 'year' => date("Y"), 'sale' => '50%' );

        if (isset($data['id'])) {

            $result             = array();   
            $badge_ID           = $data['id'];
            $label_options      = get_post_meta($badge_ID, 'badge_label_options', true);
            $style_options      = get_post_meta($badge_ID, 'badge_style_options', true);
            $position_options   = get_post_meta($badge_ID, 'badge_position_options', true);
            $preview_options    = get_post_meta($badge_ID, 'badge_preview_options', true);
            $pschedule          = get_post_meta($badge_ID, 'badge_use_pschedule', true);
            $selectedList       = get_post_meta($badge_ID, 'badge_selected_list', true); 

            // Scheduling dates
            if(get_post_meta($badge_ID, 'badge_schedules', true)){
                $schedules = unserialize(get_post_meta($badge_ID, 'badge_schedules', true));
            } else if(get_post_meta($badge_ID, 'badge_start_date', true) && get_post_meta($badge_ID, 'badge_end_date', true)){ // data before scheduling
                $schedules[0]['start_date'] = get_post_meta($badge_ID, 'badge_start_date', true);
                $schedules[0]['end_date'] = get_post_meta($badge_ID, 'badge_end_date', true);
            }

            $PListID = (int)get_post_meta($badge_ID, 'badge_product_list', true);
            $select_array[] = array ( 'label' => 'Any Product', 'value' => '' ); 
            $select_array[] = array ( 'label' => get_the_title($PListID), 'value' => $PListID ); 

            /* 
            * ML Integration
            * @ ver 1.1.2
            */
            if ( $currentLang ) { 
                $langLabel  = array_key_exists ( 'badgeLabelLang', $label_options ) ? $label_options['badgeLabelLang'] : [];
                $MLBadge    = !empty ( $langLabel ) ? ( array_key_exists ( $currentLang, $langLabel ) ? $langLabel[$currentLang] : $label_options['badgeLabel'] ) : $label_options['badgeLabel'];
            } else {
                $MLBadge    = $label_options['badgeLabel'];
            } 

            // wordpress timezone settings
            $gmt_offset = get_option('gmt_offset');
            $timezone_string = get_option('timezone_string');
            if( $timezone_string ) { 
                $datenow = new DateTime(current_time('mysql'), new DateTimeZone($timezone_string));
            } else { 
                $min    = 60 * get_option('gmt_offset'); 
                $sign   = $min < 0 ? "-" : "+";
                $absmin = abs($min); 
                $tz     = sprintf("%s%02d%02d", $sign, $absmin/60, $absmin%60); 
                $datenow = new DateTime(current_time('mysql'), new DateTimeZone($tz)); 
            }
            // $datenow->setTimezone(new DateTimeZone('+000')); // Converting to UTC+000 (moment isoString timezone)
            $datenow    = $datenow->format('F d, Y H:i'); // gmdate('F d, Y H:i'),

            $result = Array(
                'badgeTitle'                => get_the_title($badge_ID),
                'id'                        => $badge_ID,
                'badgeStatus'               => get_post_meta($badge_ID, 'badge_status', true),

                'badgeLabel'                => $MLBadge,
                'badgeLabelColor'           => $label_options['badgeLabelColor'],
                'badgeLabelColorHex'        => $label_options['badgeLabelColorHex'],
                'fontSize'                  => $label_options['fontSize'],
                'lineHeight'                => $label_options['lineHeight'],

                'badgeStyle'                => $style_options['badgeStyle'],
                'badgeColor'                => $style_options['badgeColor'],
                'badgeColorHex'             => $style_options['badgeColorHex'],
                'badgeWidth'                => $style_options['badgeWidth'],
                'badgeHeight'               => $style_options['badgeHeight'],
                'borderTopLeft'             => $style_options['borderTopLeft'],
                'borderTopRight'            => $style_options['borderTopRight'],
                'borderBottomLeft'          => $style_options['borderBottomLeft'],
                'borderBottomRight'         => $style_options['borderBottomRight'],
                'zIndex'                    => array_key_exists ( 'zIndex', $style_options ) ? $style_options['zIndex'] : '',

                'opacity'                   => $position_options['opacity'],
                'rotationX'                 => $position_options['rotationX'],
                'rotationY'                 => $position_options['rotationY'],
                'rotationZ'                 => $position_options['rotationZ'],
                'flipHorizontal'            => $position_options['flipHorizontal'],
                'flipVertical'              => $position_options['flipVertical'],
                'badgePosition'             => $position_options['badgePosition'],
                'badgePositionHorizontal'   => $position_options['badgePositionHorizontal'],
                'posTop'                    => $position_options['posTop'],
                'posBottom'                 => $position_options['posBottom'],
                'posLeft'                   => $position_options['posLeft'],
                'posRight'                  => $position_options['posRight'],
                
                'schedules'                 => $schedules,
                'productSchedule'           => $pschedule,
                'serverDateTime'            => $datenow,

                'assignAll'                 => $preview_options['assignAll'],
                'previewProductList'        => $preview_options['previewSelected'] ? array ( 'label' => get_the_title($preview_options['previewSelected']), 'value' => $preview_options['previewSelected'] ) : '',
                'previewProductThumb'       => $preview_options['previewSelected'] ? get_the_post_thumbnail_url( $preview_options['previewSelected'], 'post-thumbnail' ) : '',
                'previewSelected'           => $preview_options['previewSelected'],
                
                'selectedList'              => $selectedList,

                'dynamicLabels'             => $dynamicLabels,

                'listUrl'                   => admin_url('admin.php?page=acoplw_product_lists_ui#/')
            );

            return new WP_REST_Response($result, 200);

        }

        $all_listings = get_posts ( array ( 'fields' => 'ids','posts_per_page' => -1, 'post_type' => ACOPLW_POST_TYPE ) );
        $result = array();

        foreach ($all_listings as $listID) {
            $date1              = get_post_meta($listID, 'badge_start_date', true);
            $date2              = get_post_meta($listID, 'badge_end_date', true);
            $style_options      = get_post_meta($listID, 'badge_style_options', true);
            $label_options      = get_post_meta($listID, 'badge_label_options', true);
            $position_options   = get_post_meta($listID, 'badge_position_options', true);
            $preview_options    = get_post_meta($listID, 'badge_preview_options', true);

            if (!isset($date2) || $date2 == ''){
                $badge_schedule = 'Starts '.date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'j M Y') == date_format(date_create($date2), 'j M Y')){
                $badge_schedule = date_format(date_create($date1), 'jS M Y');
            } else if (date_format(date_create($date1), 'M Y') == date_format(date_create($date2), 'M Y')){
                $badge_schedule = date_format(date_create($date1), 'jS') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else if (date_format(date_create($date1), 'Y') == date_format(date_create($date2), 'Y')){
                $badge_schedule = date_format(date_create($date1), 'jS M') . ' - '. date_format(date_create($date2), 'jS M Y');
            } else {
                $badge_schedule = date_format(date_create($date1), 'j M Y') . ' - '. date_format(date_create($date2), 'j M Y');
            }

            // Multi Lang
            if ( $currentLang ) { 
                $langLabel          = array_key_exists ( 'badgeLabelLang', $label_options ) ? $label_options['badgeLabelLang'] : [];
                $MLBadgeLabel       = !empty ( $langLabel ) ? ( array_key_exists ( $currentLang, $langLabel ) ? $langLabel[$currentLang] : $label_options['badgeLabel'] ) : ( ( $label_options['badgeLabel'] != '' ) ? $label_options['badgeLabel'] : get_the_title ( $acoplwID ) );
            } else {
                $MLBadgeLabel       = $label_options['badgeLabel'];
            } 

            $result[] = Array(
                'badgeID'               => $listID,
                'badgeTitle'            => get_the_title($listID),
                'badgeStatus'           => get_post_meta($listID, 'badge_status', true),
                'badgeDate'             => get_the_date('d M Y', $listID),

                'badgeLabel'            => $MLBadgeLabel,
                'badgeLabelColor'       => $label_options['badgeLabelColor'],
                'badgeStyle'            => $style_options['badgeStyle'],
                'badgeColor'            => $style_options['badgeColor'],

                'fontSize'              => $label_options['fontSize'],
                'lineHeight'            => $label_options['lineHeight'],

                'badgeWidth'            => $style_options['badgeWidth'],
                'badgeHeight'           => $style_options['badgeHeight'],
                'borderTopLeft'         => $style_options['borderTopLeft'],
                'borderTopRight'        => $style_options['borderTopRight'],
                'borderBottomLeft'      => $style_options['borderBottomLeft'],
                'borderBottomRight'     => $style_options['borderBottomRight'],

                'opacity'               => $position_options['opacity'],
                'rotationX'             => $position_options['rotationX'],
                'rotationY'             => $position_options['rotationY'],
                'rotationZ'             => $position_options['rotationZ'],
                'flipHorizontal'        => $position_options['flipHorizontal'],
                'flipVertical'          => $position_options['flipVertical'],

                'assignAll'             => $preview_options['assignAll'],
                'dynamicLabels'         => $dynamicLabels
            );
        }
        return new WP_REST_Response($result, 200);
    }

    function get_list($data)
    {

        $data = $data->get_params();

        if (isset($data['id'])) {
            global $wpdb;
            $result                 = array();
            $list_item              = get_post($data['id']);
            $result['list_name']    = $list_item->post_title;
            $result['list_id']      = $list_item->ID;
            $result['list_type']    = get_post_meta($list_item->ID, 'list_type', true);
            $other_config           = get_post_meta($list_item->ID, 'product_list_config', true);

            $rules = $other_config['rules']; $tax = []; $values = ''; $ar_cnt = 1;
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

            $result['selectedProducts'] = ($other_config['selectedProducts']);
            $result['productAuthor']    = ($other_config['productAuthor']);
            $result['excludedProducts'] = ($other_config['excludedProducts']);
            $result['taxRelation']      = ($other_config['taxRelation']);
            $result['rules']            = $other_config['rules'] ? ($other_config['rules']) : '';
            $defaultProducts            = array_merge(is_array($result['excludedProducts']) ? $result['excludedProducts'] : [], is_array($result['selectedProducts']) ? $result['selectedProducts'] : []); 
            $result['defaultProducts']  = empty($defaultProducts) ? [] : $this->get_products($defaultProducts);  // used for product list suggestion dropdown

            $result['defaultTax']       = $tax;

            return new WP_REST_Response($result, 200);
        }

        $all_listings = get_posts ( array ( 'fields' => 'ids', 'numberposts' => -1, 'post_type' => ACOPLW_PRODUCT_LIST ) );
        $result = array();
        foreach ($all_listings as $listID) {
            $result[] = array(
                'list_id'   => $listID,
                'list_name' => get_the_title($listID) ? get_the_title($listID) : 'No Label',
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

        if (is_a($arg, 'WP_REST_Request')) {

            $productslist = get_posts(array('fields' => 'ids','numberposts' => -1, 'post_type' => 'product'));
            $products = Array();
            foreach ($productslist as $product) {
                if(  empty($products) || array_search ( $product, array_column ( $products, 'value' ) ) === false ) {
                    $products[] = [
                        'value' => $product,
                        'label' => get_the_title ( $product )
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
                        'label' => get_the_title ( $product )
                    ];
                }
            }
            return $products;

        }
        
    }

    function post_list($data)
    {
        $data = $data->get_params();
        $this->delete_transient();
        if ($data['id']) {
            $my_post = array(
                'ID' => $data['id'],
                'post_title' => $data['name'] ? wp_strip_all_tags($data['name']) : 'Product List',
                'post_content' => '',
            );
            wp_update_post($my_post);
            $this->update_post_list_meta($data['id'], $data);
            return $data['id'];
        } else {
            $my_post = array(
                'post_type' => ACOPLW_PRODUCT_LIST,
                'post_title' => $data['name'] ? wp_strip_all_tags($data['name']) : 'Product List',
                'post_content' => '',
                'post_status' => 'publish',
            );
            $id = wp_insert_post($my_post);
            $this->update_post_list_meta($id, $data);
            return $id;
        }
    }

    function update_post_list_meta($id, $data)
    {

        update_post_meta($id, 'list_type', $data['list_type']);
        $other_config = array(
            'selectedProducts' => ($data['selectedProducts']),
            'productAuthor' => ($data['productAuthor']),
            'excludedProducts' => ($data['excludedProducts']),
            'taxRelation' => ($data['taxRelation']),
            'rules' => ($data['rules']),
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

        $results = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts WHERE post_type in ( 'acoplw_pt_products' ) AND post_status = 'publish' AND ( post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );

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

        $results = $wpdb->get_results ( "SELECT post_title as label, ID as value, post_type as type FROM {$wpdb->prefix}posts WHERE post_type in ( 'product' ) AND post_status = 'publish' AND ( post_title LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR post_title LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' ) GROUP BY ID, post_title" );

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
        $tax = ( $params['tax'] == 'tag' ) ? 'product_tag' : 'product_cat';

        $results = $wpdb->get_results ( "SELECT cat.term_id AS value, cat.name AS label FROM {$wpdb->prefix}terms cat LEFT JOIN {$wpdb->prefix}term_taxonomy cattax ON cat.term_id = cattax.term_id WHERE cattax.taxonomy = '" . $tax . "' AND ( cat.name LIKE '" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR cat.name LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "%' OR cat.name LIKE '%" . esc_sql ( $wpdb->esc_like ( $search ) ) . "' )" );

        foreach ( $results as $result ) { 
            $result->value = (int)$result->value;
        } 

        return new WP_REST_Response($results, 200);
    }

    public function get_thumb($arg) {

        $params = $arg->get_params();
        $id = $params['id'];
        $result = [];
        $previewimage = plugin_dir_url(__FILE__). '../assets/images/preview-product.jpg';
        $thumb = get_the_post_thumbnail_url( $id, 'post-thumbnail' );
        $result['id'] = $id;
        $result['thumb'] = $thumb ? $thumb : $previewimage;
        return new WP_REST_Response($result, 200);
        
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
