<?php
/**
 * Shortcodes and usage guide tab view template.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$examples = [
	[
		'code' => '[wpcal_datetime]',
		'desc' => __( 'Current date formatted according to global default (e.g. October 10, 2026).', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime type="date"]',
		'desc' => __( 'Explicit date output.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime type="time"]',
		'desc' => __( 'Current time (e.g. 10:23 AM).', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime type="datetime"]',
		'desc' => __( 'Combined date and time (e.g. October 10, 2026 10:23 AM).', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime type="day"]',
		'desc' => __( 'Full name of current day (e.g. Saturday).', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime type="month"]',
		'desc' => __( 'Full name of current month (e.g. October).', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime type="year"]',
		'desc' => __( 'Current 4-digit year (e.g. 2026).', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime offset="+1 day"]',
		'desc' => __( 'Tomorrow\'s date (relative day offset).', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime offset="-1 day"]',
		'desc' => __( 'Yesterday\'s date.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime offset="+7 days"]',
		'desc' => __( 'Date 7 days in the future (great for weekly promotions and refund periods).', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime format="F j, Y"]',
		'desc' => __( 'Custom PHP date format.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime type="datetime" format="F j, Y g:i A"]',
		'desc' => __( 'Custom combined date and time format.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime timezone="site"]',
		'desc' => __( 'Output calculated in the WordPress site timezone.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime timezone="visitor"]',
		'desc' => __( 'Output dynamically localized to the visitor\'s browser timezone without external API requests.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime type="time" timezone="visitor" live="yes"]',
		'desc' => __( 'Live ticking clock in the visitor\'s local timezone that updates smoothly without page reloads.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_datetime preset="offer-expiry"]',
		'desc' => __( 'Load all settings from a reusable preset slug configured on the Presets tab.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_date]',
		'desc' => __( 'Convenient alias for date output.', 'wpcalibrate-dynamic-date-time' ),
	],
	[
		'code' => '[wpcal_time]',
		'desc' => __( 'Convenient alias for time output.', 'wpcalibrate-dynamic-date-time' ),
	],
];
?>
<div class="wpcal-guide-container">
	<div class="wpcal-settings-card">
		<h2><?php esc_html_e( 'Copyable Shortcode Examples', 'wpcalibrate-dynamic-date-time' ); ?></h2>
		<p class="description">
			<?php esc_html_e( 'Click "Copy" on any snippet below and paste it into WordPress posts, pages, Gutenberg shortcode blocks, Elementor Shortcode widgets, or any page builder.', 'wpcalibrate-dynamic-date-time' ); ?>
		</p>

		<div class="wpcal-snippets-grid">
			<?php foreach ( $examples as $item ) : ?>
				<div class="wpcal-snippet-card">
					<div class="wpcal-snippet-code-box">
						<code><?php echo esc_html( $item['code'] ); ?></code>
						<button type="button" class="button button-small wpcal-copy-btn" data-clipboard="<?php echo esc_attr( $item['code'] ); ?>" title="<?php esc_attr_e( 'Copy to clipboard', 'wpcalibrate-dynamic-date-time' ); ?>">
							<?php esc_html_e( 'Copy', 'wpcalibrate-dynamic-date-time' ); ?>
						</button>
					</div>
					<p class="wpcal-snippet-desc"><?php echo esc_html( $item['desc'] ); ?></p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="wpcal-settings-card">
		<h2><?php esc_html_e( 'Shortcode Attributes Reference', 'wpcalibrate-dynamic-date-time' ); ?></h2>
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Attribute', 'wpcalibrate-dynamic-date-time' ); ?></th>
					<th><?php esc_html_e( 'Default', 'wpcalibrate-dynamic-date-time' ); ?></th>
					<th><?php esc_html_e( 'Accepted Values', 'wpcalibrate-dynamic-date-time' ); ?></th>
					<th><?php esc_html_e( 'Description', 'wpcalibrate-dynamic-date-time' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><code>type</code></td>
					<td><code>date</code></td>
					<td><code>date</code>, <code>time</code>, <code>datetime</code>, <code>day</code>, <code>day_num</code>, <code>month</code>, <code>month_num</code>, <code>year</code>, <code>tomorrow</code>, <code>yesterday</code></td>
					<td><?php esc_html_e( 'Specifies the type of dynamic date or time output.', 'wpcalibrate-dynamic-date-time' ); ?></td>
				</tr>
				<tr>
					<td><code>format</code></td>
					<td><em><?php esc_html_e( 'Global Default', 'wpcalibrate-dynamic-date-time' ); ?></em></td>
					<td><?php esc_html_e( 'Standard PHP date tokens (e.g. F j, Y, g:i A, l, d/m/Y)', 'wpcalibrate-dynamic-date-time' ); ?></td>
					<td><?php esc_html_e( 'Custom date/time formatting string.', 'wpcalibrate-dynamic-date-time' ); ?></td>
				</tr>
				<tr>
					<td><code>timezone</code></td>
					<td><code>site</code></td>
					<td><code>site</code>, <code>visitor</code></td>
					<td><?php esc_html_e( 'Calculate date/time using WordPress site timezone or visitor\'s browser timezone.', 'wpcalibrate-dynamic-date-time' ); ?></td>
				</tr>
				<tr>
					<td><code>offset</code></td>
					<td><code>none</code></td>
					<td><code>+X days</code>, <code>-X days</code> (e.g. <code>+1 day</code>, <code>+7 days</code>, <code>-30 days</code>)</td>
					<td><?php esc_html_e( 'Relative calendar day offset. Safely limited to +/- 3650 days.', 'wpcalibrate-dynamic-date-time' ); ?></td>
				</tr>
				<tr>
					<td><code>live</code></td>
					<td><code>no</code></td>
					<td><code>yes</code>, <code>no</code></td>
					<td><?php esc_html_e( 'Enable live clock ticking without page reload.', 'wpcalibrate-dynamic-date-time' ); ?></td>
				</tr>
				<tr>
					<td><code>preset</code></td>
					<td><em>none</em></td>
					<td><em><?php esc_html_e( 'Valid preset slug', 'wpcalibrate-dynamic-date-time' ); ?></em></td>
					<td><?php esc_html_e( 'Load all options from a preset created in the Presets tab.', 'wpcalibrate-dynamic-date-time' ); ?></td>
				</tr>
				<tr>
					<td><code>class</code></td>
					<td><em>empty</em></td>
					<td><em><?php esc_html_e( 'CSS class names', 'wpcalibrate-dynamic-date-time' ); ?></em></td>
					<td><?php esc_html_e( 'Custom CSS class names added to the rendered <span> element.', 'wpcalibrate-dynamic-date-time' ); ?></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="wpcal-settings-card">
		<h2><?php esc_html_e( 'Attribute Precedence Order', 'wpcalibrate-dynamic-date-time' ); ?></h2>
		<ol class="wpcal-precedence-list">
			<li><strong><?php esc_html_e( '1. Explicit Attribute:', 'wpcalibrate-dynamic-date-time' ); ?></strong> <?php esc_html_e( 'Attributes passed directly in the shortcode or Gutenberg block settings take highest precedence.', 'wpcalibrate-dynamic-date-time' ); ?></li>
			<li><strong><?php esc_html_e( '2. Referenced Preset:', 'wpcalibrate-dynamic-date-time' ); ?></strong> <?php esc_html_e( 'If a preset attribute is passed, any attribute not explicitly provided is loaded from the preset.', 'wpcalibrate-dynamic-date-time' ); ?></li>
			<li><strong><?php esc_html_e( '3. Global Plugin Default:', 'wpcalibrate-dynamic-date-time' ); ?></strong> <?php esc_html_e( 'Configured on the General Settings tab.', 'wpcalibrate-dynamic-date-time' ); ?></li>
			<li><strong><?php esc_html_e( '4. Safe Built-in Fallback:', 'wpcalibrate-dynamic-date-time' ); ?></strong> <?php esc_html_e( 'Guarantees that a valid date is always output without PHP warnings.', 'wpcalibrate-dynamic-date-time' ); ?></li>
		</ol>
	</div>

	<div class="wpcal-settings-card">
		<h2><?php esc_html_e( 'Caching and Dynamic Updates Note', 'wpcalibrate-dynamic-date-time' ); ?></h2>
		<p>
			<?php esc_html_e( 'Server-rendered dates with timezone="site" and live="no" produce clean HTML that can be cached by full-page caching plugins (such as WP Rocket, LiteSpeed Cache, Super Cache, Cloudflare, etc.). When your page cache expires, WordPress regenerates the date.', 'wpcalibrate-dynamic-date-time' ); ?>
		</p>
		<p>
			<?php esc_html_e( 'When using timezone="visitor" or live="yes", the plugin outputs lightweight data attributes and enhances the element in the visitor\'s browser via native JavaScript. Even if the page is served from an edge cache or CDN, the visitor sees their accurate local time and date automatically.', 'wpcalibrate-dynamic-date-time' ); ?>
		</p>
	</div>
</div>
