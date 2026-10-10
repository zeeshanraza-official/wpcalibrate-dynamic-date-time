<?php
/**
 * Overview tab view template.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_tz         = function_exists( 'wp_timezone_string' ) ? wp_timezone_string() : 'UTC';
$active_presets  = count( $presets );
$visitor_enabled = ! empty( $settings['enable_visitor_timezone'] );
$live_enabled    = ! empty( $settings['enable_live_time'] );
?>
<div class="wpcal-overview-wrap">
	<!-- Hero Banner -->
	<div class="wpcal-hero-card">
		<div class="wpcal-hero-content">
			<div class="wpcal-hero-tag"><?php esc_html_e( 'Official Documentation & Guide', 'wpcalibrate-dynamic-date-time' ); ?></div>
			<h2><?php esc_html_e( 'Welcome to WPCalibrate Dynamic Date & Time', 'wpcalibrate-dynamic-date-time' ); ?></h2>
			<p class="wpcal-hero-lead">
				<?php esc_html_e( 'Effortlessly display auto-updating dates, times, and deadlines across your landing pages, marketing funnels, and content without writing a single line of code.', 'wpcalibrate-dynamic-date-time' ); ?>
			</p>
			<div class="wpcal-hero-actions">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=general' ) ); ?>" class="button button-primary wpcal-btn-lg">
					<span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'Configure Settings', 'wpcalibrate-dynamic-date-time' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=presets' ) ); ?>" class="button button-secondary wpcal-btn-lg">
					<span class="dashicons dashicons-tag"></span> <?php esc_html_e( 'Manage Presets', 'wpcalibrate-dynamic-date-time' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=shortcodes' ) ); ?>" class="button button-secondary wpcal-btn-lg">
					<span class="dashicons dashicons-editor-code"></span> <?php esc_html_e( 'Copy Shortcodes', 'wpcalibrate-dynamic-date-time' ); ?>
				</a>
			</div>
		</div>
		<div class="wpcal-hero-status-card">
			<div class="wpcal-status-header">
				<span class="dashicons dashicons-dashboard"></span>
				<strong><?php esc_html_e( 'System Snapshot', 'wpcalibrate-dynamic-date-time' ); ?></strong>
			</div>
			<div class="wpcal-status-row">
				<span class="wpcal-status-label"><?php esc_html_e( 'Site Timezone:', 'wpcalibrate-dynamic-date-time' ); ?></span>
				<span class="wpcal-status-value"><code><?php echo esc_html( $site_tz ); ?></code></span>
			</div>
			<div class="wpcal-status-row">
				<span class="wpcal-status-label"><?php esc_html_e( 'Current Time:', 'wpcalibrate-dynamic-date-time' ); ?></span>
				<span class="wpcal-status-value"><strong><?php echo esc_html( DateTimeService::get_site_time_string( 'F j, Y g:i A' ) ); ?></strong></span>
			</div>
			<div class="wpcal-status-row">
				<span class="wpcal-status-label"><?php esc_html_e( 'Visitor Timezone:', 'wpcalibrate-dynamic-date-time' ); ?></span>
				<span class="wpcal-status-value">
					<?php if ( $visitor_enabled ) : ?>
						<span class="wpcal-badge wpcal-badge-success"><?php esc_html_e( 'Active', 'wpcalibrate-dynamic-date-time' ); ?></span>
					<?php else : ?>
						<span class="wpcal-badge wpcal-badge-muted"><?php esc_html_e( 'Disabled', 'wpcalibrate-dynamic-date-time' ); ?></span>
					<?php endif; ?>
				</span>
			</div>
			<div class="wpcal-status-row">
				<span class="wpcal-status-label"><?php esc_html_e( 'Live Clocks:', 'wpcalibrate-dynamic-date-time' ); ?></span>
				<span class="wpcal-status-value">
					<?php if ( $live_enabled ) : ?>
						<span class="wpcal-badge wpcal-badge-success"><?php esc_html_e( 'Active', 'wpcalibrate-dynamic-date-time' ); ?></span>
					<?php else : ?>
						<span class="wpcal-badge wpcal-badge-muted"><?php esc_html_e( 'Disabled', 'wpcalibrate-dynamic-date-time' ); ?></span>
					<?php endif; ?>
				</span>
			</div>
			<div class="wpcal-status-row">
				<span class="wpcal-status-label"><?php esc_html_e( 'Saved Presets:', 'wpcalibrate-dynamic-date-time' ); ?></span>
				<span class="wpcal-status-value"><span class="wpcal-badge wpcal-badge-info"><?php printf( _n( '%d Preset', '%d Presets', $active_presets, 'wpcalibrate-dynamic-date-time' ), $active_presets ); ?></span></span>
			</div>
		</div>
	</div>

	<!-- 3 Columns: Purpose, Methods, How to Use -->
	<div class="wpcal-overview-grid">
		<!-- Purpose Card -->
		<div class="wpcal-feature-card">
			<div class="wpcal-card-icon-wrap wpcal-icon-purple">
				<span class="dashicons dashicons-awards"></span>
			</div>
			<h3><?php esc_html_e( 'Plugin Purpose', 'wpcalibrate-dynamic-date-time' ); ?></h3>
			<p>
				<?php esc_html_e( 'Eliminate stale landing-page dates and missed conversion deadlines forever.', 'wpcalibrate-dynamic-date-time' ); ?>
			</p>
			<ul class="wpcal-feature-list">
				<li>
					<span class="dashicons dashicons-yes-alt text-success"></span>
					<strong><?php esc_html_e( 'Automate Urgency & Deadlines:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'Display dynamic tomorrow or +3 day expiry dates that shift automatically every midnight.', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
				<li>
					<span class="dashicons dashicons-yes-alt text-success"></span>
					<strong><?php esc_html_e( 'Visitor-Centric Local Time:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'Ensure international buyers see dates formatted in their own timezone, eliminating confusion.', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
				<li>
					<span class="dashicons dashicons-yes-alt text-success"></span>
					<strong><?php esc_html_e( 'Zero Code Required:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'Use intuitive Gutenberg controls or one-line copyable shortcodes anywhere.', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
			</ul>
		</div>

		<!-- Methods Card -->
		<div class="wpcal-feature-card">
			<div class="wpcal-card-icon-wrap wpcal-icon-blue">
				<span class="dashicons dashicons-networking"></span>
			</div>
			<h3><?php esc_html_e( 'Methods of Operation', 'wpcalibrate-dynamic-date-time' ); ?></h3>
			<p>
				<?php esc_html_e( 'Built to deliver high performance and maximum caching compatibility.', 'wpcalibrate-dynamic-date-time' ); ?>
			</p>
			<ul class="wpcal-feature-list">
				<li>
					<span class="dashicons dashicons-admin-site-alt3 text-primary"></span>
					<strong><?php esc_html_e( 'Site Timezone Mode:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'Renders on the server using your WordPress site timezone. 100% compatible with static page caches (WP Rocket, LiteSpeed, Cloudflare).', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
				<li>
					<span class="dashicons dashicons-location text-primary"></span>
					<strong><?php esc_html_e( 'Visitor Timezone Mode:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'Detects the visitor browser timezone via native Intl APIs without slow IP geolocation or external APIs.', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
				<li>
					<span class="dashicons dashicons-clock text-primary"></span>
					<strong><?php esc_html_e( 'Centralized Live Ticker:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'A single lightweight timer loop updates all live clocks on page without CPU strain or screen-reader spam.', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
			</ul>
		</div>

		<!-- How to Use Card -->
		<div class="wpcal-feature-card">
			<div class="wpcal-card-icon-wrap wpcal-icon-green">
				<span class="dashicons dashicons-welcome-learn-more"></span>
			</div>
			<h3><?php esc_html_e( 'How to Use', 'wpcalibrate-dynamic-date-time' ); ?></h3>
			<p>
				<?php esc_html_e( 'Three flexible ways to implement dynamic dates across any WordPress site.', 'wpcalibrate-dynamic-date-time' ); ?>
			</p>
			<ul class="wpcal-feature-list">
				<li>
					<span class="dashicons dashicons-block-default text-success"></span>
					<strong><?php esc_html_e( '1. Native Gutenberg Block:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'Search for "Dynamic Date & Time" in the block inserter. Tweak type, format, and offsets in the sidebar.', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
				<li>
					<span class="dashicons dashicons-shortcode text-success"></span>
					<strong><?php esc_html_e( '2. Universal Shortcodes:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'Paste [wpcal_datetime] or aliases [wpcal_date], [wpcal_time] into Elementor, Divi, Bricks, or widgets.', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
				<li>
					<span class="dashicons dashicons-tag text-success"></span>
					<strong><?php esc_html_e( '3. Reusable Presets:', 'wpcalibrate-dynamic-date-time' ); ?></strong>
					<?php esc_html_e( 'Save common configurations on the Presets tab and reference them with [wpcal_datetime preset="offer-expiry"].', 'wpcalibrate-dynamic-date-time' ); ?>
				</li>
			</ul>
		</div>
	</div>

	<!-- Step-by-Step Quickstart Section -->
	<div class="wpcal-settings-card wpcal-quickstart-card">
		<div class="wpcal-card-header">
			<h2><?php esc_html_e( 'Quickstart: 3 Simple Steps', 'wpcalibrate-dynamic-date-time' ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Get dynamic dates live on your pages in less than two minutes.', 'wpcalibrate-dynamic-date-time' ); ?>
			</p>
		</div>

		<div class="wpcal-steps-row">
			<div class="wpcal-step-item">
				<div class="wpcal-step-badge">1</div>
				<h4><?php esc_html_e( 'Pick Your Format', 'wpcalibrate-dynamic-date-time' ); ?></h4>
				<p><?php esc_html_e( 'Choose from pre-configured global formats or use standard PHP date tokens like F j, Y or g:i A.', 'wpcalibrate-dynamic-date-time' ); ?></p>
				<code>[wpcal_datetime format="l, F j"]</code>
			</div>

			<div class="wpcal-step-item">
				<div class="wpcal-step-badge">2</div>
				<h4><?php esc_html_e( 'Add Relative Offsets', 'wpcalibrate-dynamic-date-time' ); ?></h4>
				<p><?php esc_html_e( 'Need tomorrow, 3 days ahead, or yesterday? Add an offset safely without manual adjustments.', 'wpcalibrate-dynamic-date-time' ); ?></p>
				<code>[wpcal_datetime offset="+3 days"]</code>
			</div>

			<div class="wpcal-step-item">
				<div class="wpcal-step-badge">3</div>
				<h4><?php esc_html_e( 'Publish Anywhere', 'wpcalibrate-dynamic-date-time' ); ?></h4>
				<p><?php esc_html_e( 'Insert into landing page copy, Elementor button labels, Gutenberg text blocks, or countdown banners.', 'wpcalibrate-dynamic-date-time' ); ?></p>
				<span class="wpcal-badge wpcal-badge-success"><?php esc_html_e( 'Elementor & Gutenberg Ready', 'wpcalibrate-dynamic-date-time' ); ?></span>
			</div>
		</div>
	</div>
</div>
