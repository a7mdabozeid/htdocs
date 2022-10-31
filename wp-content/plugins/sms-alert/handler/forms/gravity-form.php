<?php
/**
 * This file handles gravity form smsalert notification
 *
 * @package sms-alert/handler/forms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
GFForms::include_feed_addon_framework();

/**
 * GF_SMS_Alert class.
 */
class GF_SMS_Alert extends GFFeedAddOn {

	/**
	 * Add on version
	 *
	 * @var stirng
	 */
	protected $_version = '2.0.0';

	/**
	 * Add on min_gravityforms_version
	 *
	 * @var stirng
	 */
	protected $_min_gravityforms_version = '1.8.20';

	/**
	 * Add on gravity and smsalert slug
	 *
	 * @var stirng
	 */
	protected $_slug = 'gravity-forms-sms-alert';

	/**
	 * Add full path
	 *
	 * @var stirng
	 */
	protected $_full_path = __FILE__;

	/**
	 * Addon title
	 *
	 * @var stirng
	 */
	protected $_title = 'SMS Alert';

	/**
	 * Addon short title for addon.
	 *
	 * @var stirng
	 */
	protected $_short_title = 'SMS Alert';

	/**
	 * Check mutliple feed allowed or not.
	 *
	 * @var bool
	 */
	protected $_multiple_feeds = false;

	/**
	 * Instance for smsalert addon.
	 *
	 * @var object
	 */
	private static $_instance = null;

	/**
	 * Get instance for gravity form.
	 *
	 * @return object
	 */
	public static function get_instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Set feed setting title.
	 *
	 * @return object
	 */
	public function feed_settings_title() {
		return __( 'SMS ALERT', 'smsalert-gravity-forms' );
	}

	/**
	 * Set feed setting fields.
	 *
	 * @return array
	 */
	public function feed_settings_fields() {
		return array(
			array(
				'title'  => 'Customer SMS Settings',
				'fields' => array(
					array(
						'label'             => 'Customer Numbers',
						'type'              => 'text',
						'name'              => 'smsalert_gForm_cstmer_nos',
						'tooltip'           => 'Enter Customer Numbers',
						'class'             => 'medium merge-tag-support mt-position-right',
						'feedback_callback' => array( $this, 'is_valid_setting' ),
					),
					array(
						'label'   => 'Customer Templates',
						'type'    => 'textarea',
						'name'    => 'smsalert_gForm_cstmer_text',
						'tooltip' => 'Enter your Customer SMS Content',
						'class'   => 'medium merge-tag-support mt-position-right',
					),
				),
			),
			array(
				'title'  => 'Admin SMS Settings',
				'fields' => array(
					array(
						'label'             => 'Admin Numbers',
						'type'              => 'text',
						'name'              => 'smsalert_gForm_admin_nos',
						'tooltip'           => 'Enter admin Numbers',
						'class'             => 'medium merge-tag-support mt-position-right',
						'feedback_callback' => array( $this, 'is_valid_setting' ),
					),
					array(
						'label'   => 'Admin Templates',
						'type'    => 'textarea',
						'name'    => 'smsalert_gForm_admin_text',
						'tooltip' => 'Enter your admin SMS Content',
						'class'   => 'medium merge-tag-support mt-position-right',
					),
				),
			),
		);
	}

	/**
	 * Handle form submission and send message to customer and admin.
	 *
	 * @param array $entry form entry.
	 * @param array $form form fields.
	 *
	 * @return void
	 */
	public static function do_gForm_processing( $entry, $form ) {
		$message    = '';
		$cstmer_nos = '';
		$admin_nos  = '';
		$admin_msg  = '';
		$meta       = RGFormsModel::get_form_meta( $entry['form_id'] );
		$feeds      = GFAPI::get_feeds( null, $entry['form_id'], 'gravity-forms-sms-alert' );
		foreach ( $feeds as $feed ) {
			if ( count( $feed ) > 0 && array_key_exists( 'meta', $feed ) ) {
				$admin_msg          = $feed['meta']['smsalert_gForm_admin_text'];
				$admin_nos          = $feed['meta']['smsalert_gForm_admin_nos'];
				$cstmer_nos_pattern = $feed['meta']['smsalert_gForm_cstmer_nos'];
				$message            = $feed['meta']['smsalert_gForm_cstmer_text'];
			}
		}

		foreach ( $meta['fields'] as $meta_field ) {
			if ( is_object( $meta_field ) ) {
				$field_id = $meta_field->id;
				if ( isset( $entry[ $field_id ] ) ) {
					$label     = $meta_field->label;
					$search    = '{' . $label . ':' . $field_id . '}';
					$replace   = $entry[ $field_id ];
					$message   = str_replace( $search, $replace, $message );
					$admin_msg = str_replace( $search, $replace, $admin_msg );

					if ( $cstmer_nos_pattern === $search ) {
						$cstmer_nos = $replace;
					}
				}
			}
		}
		if ( ! empty( $cstmer_nos ) && ! empty( $message ) ) {
			do_action( 'sa_send_sms', $cstmer_nos, $message );
		}
		if ( ! empty( $admin_nos ) && ! empty( $admin_msg ) ) {
			do_action( 'sa_send_sms', $admin_nos, $admin_msg );
		}
	}
}
new GF_SMS_Alert();

add_action( 'gform_after_submission', array( 'GF_SMS_Alert', 'do_gForm_processing' ), 10, 2 );
