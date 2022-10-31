<?php

	/**
	 * Fired during plugin activation
	 *
	 * @link       https://codeboxr.com
	 * @since      1.0.0
	 *
	 * @package    CBXUserOnline
	 * @subpackage CBXUserOnline/includes
	 */

	/**
	 * Fired during plugin activation.
	 *
	 * This class defines all code necessary to run during the plugin's activation.
	 *
	 * @since      1.0.0
	 * @package    CBXUserOnline
	 * @subpackage CBXUserOnline/includes
	 * @author     codeboxr <info@codeboxr.com>
	 */
	class CBXUserOnline_Activator {

		/**
		 * Short Description. (use period)
		 *
		 * Long Description.
		 *
		 * @since    1.0.0
		 */
		public static function activate() {
			//create tables
			CBXUserOnlineHelper::create_tables();

			set_transient( 'cbxuseronline_activated_notice', 1 );

		}//end method activate

	}//end class CBXUserOnline_Activator
