<?php

/**
 * Plugin main class.
 *
 * @package InvoicesWooCommerce
 */

namespace WPDesk\WPDeskFRFree;

use FRFreeVendor\WPDesk\Dashboard\DashboardWidget;
use FRFreeVendor\WPDesk\Library\FlexibleRefundsCore\Integration;
use FRFreeVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use FRFreeVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use FRFreeVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use FRFreeVendor\WPDesk_Plugin_Info;

/**
 * Main plugin class. The most important flow decisions are made here.
 */
class Plugin extends AbstractPlugin implements HookableCollection {

	use HookableParent;

	/**
	 * @param WPDesk_Plugin_Info $plugin_info Plugin data.
	 */
	public function __construct( $plugin_info ) {
		$this->plugin_info = $plugin_info;
		parent::__construct( $this->plugin_info );

		$this->docs_url     = get_locale() === 'pl_PL' ? 'https://wpde.sk/flexible-refunds-for-woocommerce' : 'https://wpde.sk/flexible-refunds-for-woocommerce-docs';
		$this->support_url  = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/support/' : 'https://www.wpdesk.net/support/';
		$this->settings_url = admin_url( 'admin.php?page=wc-settings&tab=flexible_refunds' );
	}

	/**
	 * Integrate with WordPress and with other plugins using action/filter system.
	 *
	 * @return void
	 */
	public function hooks() {
		parent::hooks();
		$this->add_hookable( new Integration() );
		$this->add_hookable( new DeactivateFree() );
		$this->hooks_on_hookable_objects();
		( new DashboardWidget() )->hooks();
	}
}
