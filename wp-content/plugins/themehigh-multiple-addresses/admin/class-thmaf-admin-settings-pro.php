<?php
/**
 * The pro version specification describing file.
 *
 * @link       https://themehigh.com
 * @since      1.0.0
 *
 * @package    themehigh-multiple-addresses
 * @subpackage themehigh-multiple-addresses/admin
 */

if(!defined('WPINC')) { 
    die; 
}

if(!class_exists('THMAF_Admin_Settings_Pro')):

	/**
     * Genereal setting class.
    */
	class THMAF_Admin_Settings_Pro extends THMAF_Admin_Settings {
		protected static $_instance = null;

		private $section_form = null;
		private $field_form = null;

		private $field_props = array();

		/**
         * Constructor.
         */
		public function __construct() {
			parent::__construct('pro', '');
			$this->page_id = 'pro';
			//$this->init_constants();
		}

		public static function instance() {
			if(is_null(self::$_instance)){
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
		public function render_page(){
			$this->render_tabs();
			$this->render_content();
		}

		private function render_content(){
			?>
			<div class="th-wrap-pro">
				<div class="th-nice-box">
				    <h2>MULTIPLE SHIPPING ADDRESSES FOR WOOCOMMERCE PREMIUM TAB FEATURES</h2>
				    <p>The Premium Version of Multiple Shipping Addresses adds extra features to help you run your store efficiently and allows your customers to shop more swiftly.</p>	
				    <p>
				    	<a class="button big-button" target="_blank" href="https://www.themehigh.com/product/woocommerce-multiple-addresses-pro/?utm_source=free&utm_medium=premium_tab&utm_campaign=wmap_upgrade_link/">Upgrade to Premium Version</a>
				    	<a class="button big-button" target="_blank" href="https://flydemos.com/wmap/?utm_source=free&utm_medium=premium_tab&utm_campaign=wmap_try_demo" style="margin-left: 20px">Try Demo</a>
					</p>
				</div>
				<div class="th-flexbox">
				    <div class="th-nice-box">
				        <h2>Key Features</h2>
				        <ul class="feature-list star-list">
				            <li><b>Multi Shipping:</b> 
				            	<p>In a single order, users can ship different products to multiple addresses.</p>
				            </li>
				            <li><b>Google Map AutoComplete:</b> 
				            	<p>The Google Map AutoComplete feature helps the users search locations quickly.</p>
				            </li>
				            <li><b>Manage Addresses:</b> 
				            	<p>From My Address Page, customers can modify their saved addresses, as well as add new ones.</p>
				            </li>
				            <li><b>Exclude from Multi-Shipping Properties:</b> 
				            	<p>Multi-shipping options can be restricted for certain products and product categories.</p>
				            </li>
				            <li><b>Split Order Status:</b> 
				            	<p>Each item's delivery status can be split into distinct individual order status within a single order.</p>
				            </li>
				            <li><b>Address Format:</b> 
				            	<p>Using the address override feature, customize the WooCommerce default address formats.</p>
				            </li>
				            <li><b>Style Multiple Address Layouts:</b> 
				            	<p>Choose from a range of styles to personalise the multiple address option at checkout.</p>
				            </li>
				            <li><b>Default Shipping Address:</b> 
				            	<p>Any saved addresses can be set as the default address for easy access.</p>
				            </li>
				            <li><b>Manage Address from Checkout Page:</b> 
				            	<p>All saved addresses can be accessed from the Checkout page.</p>
				            </li>
				        </ul>
				        
				    </div>
				    <div class="th-nice-box">
				        <h2>Additional Features</h2>
				        <ul class="feature-list">
				            <li>
				            	<b>Guest User Privileges</b>
				            	<p>Assign multi-shipping privileges to guest users. </p>
				            </li>
				            <li>
				            	<b>Label Styling</b>
				            	<p>The address picking URL and Label can be renamed and customized.</p>
				            </li>
				            <li>
				            	<b>Backup & Import</b>
				            	<p>The Backup & Import feature facilitates you to copy the existing plugin configurations into a Multiple Shipping Addresses  plugin on another WordPress store.</p>
				            </li>
				        </ul>
				    </div>
				</div>				
			</div>
			<?php
		}
	}
endif;