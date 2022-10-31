<?php
// add tabby checkout api configuration tab
/*
add_filter( 'woocommerce_get_sections_checkout', 'add_tabby_checkout_api_tab' );
function add_tabby_checkout_api_tab( $sections ) {
	
	$sections['tabby_api'] = __( 'Tabby Checkout API', 'tabby-checkout' );
	return $sections;
	
}
*/
/**
 * Add settings to the specific section we created before
 */
//add_filter( 'woocommerce_get_settings_checkout', 'tabby_checkout_api_settings', 10, 2 );
function tabby_checkout_api_settings( $settings, $current_section ) {
	if ( $current_section == 'tabby_api' ) {
		$settings_tabby = array();
		$settings_tabby[] = array( 
			'name' => __( 'Tabby Checkout API', 'tabby-checkout' ), 
			'type' => 'title', 
			'desc' => __( 'The following options are used to configure Tabby Checkout API', 'tabby-checkout' ), 
			'id' => 'tabby-checkout'
		);
		$settings_tabby[] = array(
			'name'     => __( 'Merchant Public Key', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_public_key',
			'type'     => 'password',
			'desc'     => __( 'Used for public API calls', 'tabby-checkout' ),
		);
		$settings_tabby[] = array(
			'name'     => __( 'Merchant Secret Key', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_secret_key',
			'type'     => 'password',
			'desc'     => __( 'Used for server to server calls', 'tabby-checkout' ),
		);
		$settings_tabby[] = array(
			'name'     => __( 'Tabby Language', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_popup_language',
			'type'     => 'select',
			'desc'     => __( 'Tabby popup language', 'tabby-checkout' ),
            'options'  => [
                'auto'  => __('Auto', 'tabby-checkout'),
                'en'    => __('English', 'tabby-checkout'),
                'ar'    => __('Arabic', 'tabby-checkout')
            ],
            'default'   => 'auto'
		);
		$settings_tabby[] = array(
			'name'     => __( 'Tabby order timeout, mins', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_order_timeout',
			'type'     => 'text',
			'desc'     => __( 'Used for deleting unpaid orders', 'tabby-checkout' ),
            'type'              => 'number',
            'custom_attributes' => array(
                'min'  => 0,
                'step' => 1,
            ),
            'css'               => 'width: 80px;',
            'default'           => '20',
            'autoload'          => false,
		);
		$settings_tabby[] = array(
			'name'     => __( 'Action on Failed Payment', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_failed_action',
			'type'     => 'text',
			'desc'     => __( 'Delete or cancel unpaid orders', 'tabby-checkout' ),
            'type'     => 'select',
            'options'  => [
                'delete' => __('Cancel & Delete', 'tabby-checkout'),
                'trash'  => __('Cancel & Move to Trash', 'tabby-checkout'),
                'cancel' => __('Cancel Only', 'tabby-checkout')
            ],
            'css'               => 'width: 180px;',
            'default'           => 'delete'
		);
		$settings_tabby[] = array(
			'name'     => __( 'Use phone number for order history lookup', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_use_phone',
			'type'     => 'checkbox',
			'desc'     => __( 'Add order history by phone', 'tabby-checkout' ),
            'default'  => 'yes'
		);
		$settings_tabby[] = array(
			'name'     => __( 'Capture payment on checkout', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_capture',
			'type'     => 'checkbox',
			'desc'     => __( 'Enable payment capture on checkout', 'tabby-checkout' ),
            'default'  => 'yes'
		);
		$settings_tabby[] = array(
			'name'     => __( 'Tabby promotions (product)', 'tabby-checkout' ),
			'id'       => 'tabby_promo',
			'type'     => 'checkbox',
			'desc'     => __( 'Enable Tabby promo on product view pages', 'tabby-checkout' ),
            'default'   => 'yes'
		);
		$settings_tabby[] = array(
			'name'     => __( 'Tabby promotions (cart page)', 'tabby-checkout' ),
			'id'       => 'tabby_promo_cart',
			'type'     => 'checkbox',
			'desc'     => __( 'Enable Tabby promo on shopping cart page', 'tabby-checkout' ),
            'default'   => 'yes'
		);
		$settings_tabby[] = array(
			'name'     => __( 'Minimum price for Tabby promo', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_promo_min_total',
			'type'     => 'text',
			'desc'     => __( 'Minimum price showing Tabby promotions, 0 for unlimited', 'tabby-checkout' ),
            'type'              => 'number',
            'custom_attributes' => array(
                'min'  => 0,
                'step' => 1,
            ),
            'css'               => 'width: 80px;',
            'default'           => '0',
            'autoload'          => false,
		);
		$settings_tabby[] = array(
			'name'     => __( 'Tabby promo max price', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_promo_price',
			'type'     => 'text',
			'desc'     => __( 'Maximum product price for showing Tabby promotions, 0 for unlimited', 'tabby-checkout' ),
            'type'              => 'number',
            'custom_attributes' => array(
                'min'  => 0,
                'step' => 1,
            ),
            'css'               => 'width: 80px;',
            'default'           => '0',
            'autoload'          => false,
		);
		$settings_tabby[] = array(
			'name'     => __( 'Tabby promotions theme', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_promo_theme',
			'type'     => 'text',
			'desc'     => __( 'Used for styling Tabby promotions widget (blank for default)', 'tabby-checkout' ),
		);
        $countries = new WC_Countries();
        $options = [];
        foreach (WC_Tabby_Config::ALLOWED_COUNTRIES as $code) {
            $options[$code] = __($countries->countries[$code], 'tabby-checkout'); 
        }
        asort($options);
		$settings_tabby[] = array(
			'name'     => __( 'Allowed Countries', 'tabby-checkout' ),
			'id'       => 'tabby_countries',
			'type'     => 'multiselect',
			'desc'     => __( 'Tabby allowed countries', 'tabby-checkout' ),
            'options'  => $options,
            'default'  => WC_Tabby_Config::ALLOWED_COUNTRIES
		);
		$settings_tabby[] = array(
			'name'     => __( 'Use <html> tag lang', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_locale_html',
			'type'     => 'checkbox',
			'desc'     => __( 'Use documentElement lang attribute for tabby language', 'tabby-checkout' ),
		);
		$settings_tabby[] = array(
			'name'     => __( 'Send extended merchant code', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_extended_code',
			'type'     => 'checkbox',
			'desc'     => __( 'Change merchant code format to &lt;country&gt;_&lt;currency&gt;', 'tabby-checkout' ),
		);
		$settings_tabby[] = array(
			'name'     => __( 'Hide methods', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_hide_methods',
			'type'     => 'checkbox',
			'desc'     => __( 'Hide Tabby payment methods on checkout if not available', 'tabby-checkout' ),
		);
		$settings_tabby[] = array(
			'name'     => __( 'Debug', 'tabby-checkout' ),
			'id'       => 'tabby_checkout_debug',
			'type'     => 'checkbox',
			'desc'     => __( 'Enable API request/reply logging', 'tabby-checkout' ),
            'default'  => 'yes'
		);
		
		$settings_tabby[] = array( 'type' => 'sectionend', 'id' => 'tabby_api' );
		return $settings_tabby;
	
	} else {
		return $settings;
	}
}

class WC_Settings_Tab_Tabby {

    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
    }

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_tab_tabby'] = __( 'Tabby API', 'tabby-checkout' );
        return $settings_tabs;
    }
}
WC_Settings_Tab_Tabby::init();

add_action( 'woocommerce_settings_tabs_settings_tab_tabby', 'tabby_settings_tab' );

function tabby_settings_tab() {
    echo "<style>#tabby_countries option {padding: 10px;}</style>";
    woocommerce_admin_fields( tabby_checkout_api_settings([], 'tabby_api') );
}

add_action( 'woocommerce_update_options_settings_tab_tabby', 'tabby_update_settings' );

function tabby_update_settings() {
    woocommerce_update_options( tabby_checkout_api_settings([], 'tabby_api') );
    WC_Tabby_Webhook::register();
}
