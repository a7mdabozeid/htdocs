<?php

namespace WPDesk\WPDeskFRFree;

use FRFreeVendor\WPDesk\PluginBuilder\Plugin\Hookable;

class DeactivateFree implements Hookable {

	public function hooks() {
		add_action( 'admin_init', [ $this, 'check_if_pro_version_is_installed' ] );
	}

	public function check_if_pro_version_is_installed() {
		if ( is_admin() && current_user_can( 'activate_plugins' ) && is_plugin_active( 'flexible-refunds-pro/flexible-refunds-pro.php' ) ) {
			add_action( 'admin_notices', [ $this, 'plugin_notice' ] );

			deactivate_plugins( plugin_basename( 'flexible-refund-and-return-order-for-woocommerce/flexible-refund-and-return-order-for-woocommerce.php' ) );
		}
	}

	public function plugin_notice() {
		$allowed_tags = [
			'p'   => [],
			'div' => [
				'class' => [],
			],
		];
		echo wp_kses( '<div class="error"><p>' . __( 'Free version of plugin Flexible Refund and Return Order for WooCommerce was deactivated because Pro version is active.', 'flexible-refund-and-return-order-for-woocommerce' ) . '</p></div>', $allowed_tags );
	}

}
