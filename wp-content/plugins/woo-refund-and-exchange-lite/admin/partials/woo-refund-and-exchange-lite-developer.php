<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to list all the hooks and filter with their descriptions.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wrael_wps_rma_obj;
$wrael_developer_admin_hooks =
// Admin Hooks.
apply_filters( 'wrael_developer_admin_hooks_array', array() );
$count_admin                  = filtered_array( $wrael_developer_admin_hooks );
$wrael_developer_public_hooks =
// Admin Hooks.
apply_filters( 'wrael_developer_public_hooks_array', array() );
$count_public = filtered_array( $wrael_developer_public_hooks );
?>
<!--  template file for admin settings. -->
<div class="wrael-section-wrap">
	<div class="wps-col-wrap">
		<div id="admin-hooks-listing" class="table-responsive mdc-data-table">
			<table class="wps-wrael-table mdc-data-table__table wps-table"  id="wps-wrael-wp">
				<thead>
				<tr><th class="mdc-data-table__header-cell"><?php esc_html_e( 'Admin Hooks', 'woo-refund-and-exchange-lite' ); ?></th></tr>
				<tr>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Type of Hook', 'woo-refund-and-exchange-lite' ); ?></th>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Hooks', 'woo-refund-and-exchange-lite' ); ?></th>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Hooks description', 'woo-refund-and-exchange-lite' ); ?></th>
				</tr>
				</thead>
				<tbody class="mdc-data-table__content">
				<?php
				if ( ! empty( $count_admin ) ) {
					foreach ( $count_admin as $key => $value ) {
						if ( isset( $value['action_hook'] ) ) {
							?>
						<tr class="mdc-data-table__row"><td class="mdc-data-table__cell"><?php esc_html_e( 'Action Hook', 'woo-refund-and-exchange-lite' ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $value['action_hook'] ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $value['desc'] ); ?></td></tr>
							<?php
						} else {
							?>
							<tr class="mdc-data-table__row"><td class="mdc-data-table__cell"><?php esc_html_e( 'Filter Hook', 'woo-refund-and-exchange-lite' ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $value['filter_hook'] ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $value['desc'] ); ?></td></tr>
							<?php
						}
					}
				} else {
					?>
					<tr class="mdc-data-table__row"><td><?php esc_html_e( 'No Hooks Found', 'woo-refund-and-exchange-lite' ); ?><td></tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="wps-col-wrap">
		<div id="public-hooks-listing" class="table-responsive mdc-data-table">
			<table class="wps-wrael-table mdc-data-table__table wps-table" id="wps-wrael-sys">
				<thead>
				<tr><th class="mdc-data-table__header-cell"><?php esc_html_e( 'Public Hooks', 'woo-refund-and-exchange-lite' ); ?></th></tr>
				<tr>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Type of Hook', 'woo-refund-and-exchange-lite' ); ?></th>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Hooks', 'woo-refund-and-exchange-lite' ); ?></th>
					<th class="mdc-data-table__header-cell"><?php esc_html_e( 'Hooks description', 'woo-refund-and-exchange-lite' ); ?></th>
				</tr>
				</thead>
				<tbody class="mdc-data-table__content">
				<?php
				if ( ! empty( $count_public ) ) {
					foreach ( $count_public as $key => $value ) {
						if ( isset( $value['action_hook'] ) ) {
							?>
						<tr class="mdc-data-table__row"><td class="mdc-data-table__cell"><?php esc_html_e( 'Action Hook', 'woo-refund-and-exchange-lite' ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $value['action_hook'] ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $value['desc'] ); ?></td></tr>
							<?php
						} else {
							?>
							<tr class="mdc-data-table__row"><td class="mdc-data-table__cell"><?php esc_html_e( 'Filter Hook', 'woo-refund-and-exchange-lite' ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $value['filter_hook'] ); ?></td><td class="mdc-data-table__cell"><?php echo esc_html( $value['desc'] ); ?></td></tr>
							<?php
						}
					}
				} else {
					?>
					<tr class="mdc-data-table__row"><td><?php esc_html_e( 'No Hooks Found', 'woo-refund-and-exchange-lite' ); ?><td></tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php

/**
 * Filter array
 *
 * @param array $argu .
 */
function filtered_array( $argu ) {
	$count_admin = array();
	foreach ( $argu as $key => $value ) {
		foreach ( $value as $key => $originvalue ) {
			if ( isset( $originvalue['action_hook'] ) ) {
				$val                                = explode( "'", $originvalue['action_hook'] );
				$val                                = isset( $val[1] ) ? $val[1] : '';
				$count_admin[ $key ]['action_hook'] = $val;
			}
			if ( isset( $originvalue['filter_hook'] ) ) {
				$val                                = explode( "'", $originvalue['filter_hook'] );
				$val                                = isset( $val[1] ) ? $val[1] : '';
				$count_admin[ $key ]['filter_hook'] = $val;
			}
			$vale                        = str_replace( '//desc - ', '', $originvalue['desc'] );
			$count_admin[ $key ]['desc'] = $vale;
		}
	}
	return $count_admin;
}

