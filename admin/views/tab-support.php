<?php
/**
 * Support tab view template.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wpcal-support-container">
	<div class="wpcal-settings-card">
		<h2><?php esc_html_e( 'Official WPCalibrate Support & Resources', 'wpcalibrate-dynamic-date-time' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Need assistance, feature requests, or have questions about using Dynamic Date & Time? Our engineering team is here to assist you.', 'wpcalibrate-dynamic-date-time' ); ?>
		</p>

		<div class="wpcal-support-cards">
			<div class="wpcal-support-card">
				<div class="wpcal-support-icon">
					<span class="dashicons dashicons-email-alt"></span>
				</div>
				<h3><?php esc_html_e( 'Direct Email Support', 'wpcalibrate-dynamic-date-time' ); ?></h3>
				<p><?php esc_html_e( 'Send questions or diagnostic requests directly to our support inbox.', 'wpcalibrate-dynamic-date-time' ); ?></p>
				<p>
					<a href="<?php echo esc_url( 'mailto:support@wpcalibrate.com' ); ?>" class="button button-secondary">
						<?php esc_html_e( 'support@wpcalibrate.com', 'wpcalibrate-dynamic-date-time' ); ?>
					</a>
				</p>
			</div>

			<div class="wpcal-support-card">
				<div class="wpcal-support-icon">
					<span class="dashicons dashicons-admin-site-alt3"></span>
				</div>
				<h3><?php esc_html_e( 'Official Website', 'wpcalibrate-dynamic-date-time' ); ?></h3>
				<p><?php esc_html_e( 'Discover documentation, tutorials, and updates on the official website.', 'wpcalibrate-dynamic-date-time' ); ?></p>
				<p>
					<a href="<?php echo esc_url( 'https://wpcalibrate.com' ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
						<?php esc_html_e( 'Visit WPCalibrate.com', 'wpcalibrate-dynamic-date-time' ); ?> &rarr;
					</a>
				</p>
			</div>

			<div class="wpcal-support-card">
				<div class="wpcal-support-icon">
					<span class="dashicons dashicons-cart"></span>
				</div>
				<h3><?php esc_html_e( 'Plugin Marketplace', 'wpcalibrate-dynamic-date-time' ); ?></h3>
				<p><?php esc_html_e( 'Explore additional performance and workflow plugins for WordPress.', 'wpcalibrate-dynamic-date-time' ); ?></p>
				<p>
					<a href="<?php echo esc_url( 'https://marketplace.wpcalibrate.com/' ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
						<?php esc_html_e( 'Visit Marketplace', 'wpcalibrate-dynamic-date-time' ); ?> &rarr;
					</a>
				</p>
			</div>

			<div class="wpcal-support-card">
				<div class="wpcal-support-icon">
					<span class="dashicons dashicons-phone"></span>
				</div>
				<h3><?php esc_html_e( 'Phone & WhatsApp', 'wpcalibrate-dynamic-date-time' ); ?></h3>
				<p><?php esc_html_e( 'Direct line and WhatsApp chat for enterprise inquiries and technical consultations.', 'wpcalibrate-dynamic-date-time' ); ?></p>
				<p>
					<a href="<?php echo esc_url( 'https://wa.me/447474795976' ); ?>" target="_blank" rel="noopener noreferrer" class="button button-secondary">
						<?php esc_html_e( '+44 7474 795976', 'wpcalibrate-dynamic-date-time' ); ?>
					</a>
				</p>
			</div>
		</div>
	</div>
</div>
