<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for overview.
 *
 * @link       https://wpswings.com/
 * @since      1.0.0
 *
 * @package    woo-refund-and-exchange-lite
 * @subpackage woo-refund-and-exchange-lite/admin/partials
 */

?>

<div class="wps-overview__wrapper">
	<div class="wps-overview__banner">
		<img src="<?php echo esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL ); ?>admin/image/banner.jpg" alt="Overview banner image">
	</div>
	<div class="wps-overview__content">
		<div class="wps-overview__content-description">
			<h2><?php echo esc_html_e( 'What Is Woo Refund And Exchange Lite?', 'woo-refund-and-exchange-lite' ); ?></h2>
			<p>
				<?php
				esc_html_e( 'Return Refund and Exchange for WooCommerce is a one-stop solution for complete refund management plugin for your WooCommerce store. This FREE plugin allows the admin to show a "Refund" button on the desired page of your store, that the customers can use to send you a refund request for their purchased product with which they are unsatisfied.', 'woo-refund-and-exchange-lite' );
				?>
			</p>
			<p>
				<?php
				esc_html_e( 'Further, this plugin has a message feature that allows merchants and customers to connect with direct messages to solve refund related issues. Admin can set refund button text, allows customers to send reasons for refund request, set predefined reason, allow attachments along with refund request, set limits to number of attachments, set condition on products if and how long it is eligible for refund, etc.', 'woo-refund-and-exchange-lite' );
				?>
			</p>
			<p>
				<?php
				esc_html_e( 'The whole process goes under a dedicated email based notification system which would keep both the parties on the same note. With WPML, the plugin can be translated into different languages, to engage multilingual buyers across the globe. ', 'woo-refund-and-exchange-lite' );
				?>
			</p>
		</div>
		<h2> <?php esc_html_e( 'The Free Plugin Benefits', 'woo-refund-and-exchange-lite' ); ?></h2>
		<div class="wps-overview__keywords">
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'admin/image/Connect-via-Messages.png' ); ?>" alt="AConnect-via-Messages image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( ' Connect via Messages ', 'woo-refund-and-exchange-lite' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'Allow customers to send you messages in the refund panel.', 'woo-refund-and-exchange-lite' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'admin/image/Allow-And-Set-Attachments-Limit.png' ); ?>" alt="Allow-And-Set-Attachments-Limit image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( ' Allow And Set Attachments Limit ', 'woo-refund-and-exchange-lite' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'Admin can allow and set a limit to the number of attachments on the refund request form.', 'woo-refund-and-exchange-lite' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'admin/image/Set-Return-Refund-Conditions.png' ); ?>" alt="Set-Return-Refund-Conditions image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( ' Set Return Refund Conditions ', 'woo-refund-and-exchange-lite' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'Admin can set conditions for the refund process.', 'woo-refund-and-exchange-lite' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'admin/image/Send-Email-Notification.png' ); ?>" alt="Send-Email-Notification image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( ' Send Email Notification' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'Set emails to notify each performing step during the refund process.', 'woo-refund-and-exchange-lite' );
							?>
						</p>
					</div>
				</div>
			</div>
			<div class="wps-overview__keywords-item">
				<div class="wps-overview__keywords-card">
					<div class="wps-overview__keywords-image">
						<img src="<?php echo esc_html( WOO_REFUND_AND_EXCHANGE_LITE_DIR_URL . 'admin/image/Manage-Product-Returns.png' ); ?>" alt="Manage-Product-Returns image">
					</div>
					<div class="wps-overview__keywords-text">
						<h3 class="wps-overview__keywords-heading"><?php echo esc_html_e( ' Manage Product Returns ', 'woo-refund-and-exchange-lite' ); ?></h3>
						<p class="wps-overview__keywords-description">
							<?php
							esc_html_e( 'Provide a complete refund system with the manage stock and the refund amount', 'woo-refund-and-exchange-lite' );
							?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
