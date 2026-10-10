<?php
/**
 * General settings tab view template.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_tz = function_exists( 'wp_timezone_string' ) ? wp_timezone_string() : 'UTC';
?>
<div class="wpcal-settings-card">
	<h2><?php esc_html_e( 'General Configuration', 'wpcalibrate-dynamic-date-time' ); ?></h2>
	<p class="description">
		<?php
		printf(
			/* translators: %s: Current site timezone */
			esc_html__( 'Configure global defaults for dynamic dates and times. Your WordPress site timezone is configured as %s.', 'wpcalibrate-dynamic-date-time' ),
			'<code>' . esc_html( $site_tz ) . '</code>'
		);
		?>
	</p>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=general' ) ); ?>" class="wpcal-form">
		<?php wp_nonce_field( 'wpcal_ddt_save_settings', 'wpcal_ddt_nonce' ); ?>
		<input type="hidden" name="wpcal_ddt_action" value="save_settings" />

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="default_date_format"><?php esc_html_e( 'Default Date Format', 'wpcalibrate-dynamic-date-time' ); ?></label>
					</th>
					<td>
						<input type="text" id="default_date_format" name="settings[default_date_format]" value="<?php echo esc_attr( $settings['default_date_format'] ?? 'F j, Y' ); ?>" class="regular-text code" />
						<p class="description">
							<?php
							printf(
								/* translators: %s: Example formatted date */
								esc_html__( 'Preview: %s (Standard PHP/WordPress date format tokens).', 'wpcalibrate-dynamic-date-time' ),
								'<strong>' . esc_html( DateTimeService::get_site_time_string( $settings['default_date_format'] ?? 'F j, Y' ) ) . '</strong>'
							);
							?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="default_time_format"><?php esc_html_e( 'Default Time Format', 'wpcalibrate-dynamic-date-time' ); ?></label>
					</th>
					<td>
						<input type="text" id="default_time_format" name="settings[default_time_format]" value="<?php echo esc_attr( $settings['default_time_format'] ?? 'g:i A' ); ?>" class="regular-text code" />
						<p class="description">
							<?php
							printf(
								/* translators: %s: Example formatted time */
								esc_html__( 'Preview: %s', 'wpcalibrate-dynamic-date-time' ),
								'<strong>' . esc_html( DateTimeService::get_site_time_string( $settings['default_time_format'] ?? 'g:i A' ) ) . '</strong>'
							);
							?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="default_datetime_format"><?php esc_html_e( 'Default Date & Time Format', 'wpcalibrate-dynamic-date-time' ); ?></label>
					</th>
					<td>
						<input type="text" id="default_datetime_format" name="settings[default_datetime_format]" value="<?php echo esc_attr( $settings['default_datetime_format'] ?? 'F j, Y g:i A' ); ?>" class="regular-text code" />
						<p class="description">
							<?php
							printf(
								/* translators: %s: Example formatted datetime */
								esc_html__( 'Preview: %s', 'wpcalibrate-dynamic-date-time' ),
								'<strong>' . esc_html( DateTimeService::get_site_time_string( $settings['default_datetime_format'] ?? 'F j, Y g:i A' ) ) . '</strong>'
							);
							?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="default_timezone_mode"><?php esc_html_e( 'Default Timezone Mode', 'wpcalibrate-dynamic-date-time' ); ?></label>
					</th>
					<td>
						<select id="default_timezone_mode" name="settings[default_timezone_mode]">
							<option value="site" <?php selected( $settings['default_timezone_mode'] ?? 'site', 'site' ); ?>>
								<?php esc_html_e( 'WordPress Site Timezone (Server rendered)', 'wpcalibrate-dynamic-date-time' ); ?>
							</option>
							<option value="visitor" <?php selected( $settings['default_timezone_mode'] ?? 'site', 'visitor' ); ?>>
								<?php esc_html_e( 'Visitor Local Timezone (Browser detected)', 'wpcalibrate-dynamic-date-time' ); ?>
							</option>
						</select>
						<p class="description">
							<?php esc_html_e( 'Can be overridden on any specific shortcode or block with timezone="site" or timezone="visitor".', 'wpcalibrate-dynamic-date-time' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<?php esc_html_e( 'Visitor Timezone Feature', 'wpcalibrate-dynamic-date-time' ); ?>
					</th>
					<td>
						<label for="enable_visitor_timezone">
							<input type="checkbox" id="enable_visitor_timezone" name="settings[enable_visitor_timezone]" value="1" <?php checked( ! empty( $settings['enable_visitor_timezone'] ) ); ?> />
							<?php esc_html_e( 'Enable visitor browser timezone detection', 'wpcalibrate-dynamic-date-time' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'When disabled, all shortcodes and blocks fall back to the WordPress site timezone.', 'wpcalibrate-dynamic-date-time' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<?php esc_html_e( 'Live Clock Feature', 'wpcalibrate-dynamic-date-time' ); ?>
					</th>
					<td>
						<label for="enable_live_time">
							<input type="checkbox" id="enable_live_time" name="settings[enable_live_time]" value="1" <?php checked( ! empty( $settings['enable_live_time'] ) ); ?> />
							<?php esc_html_e( 'Enable live ticking time functionality', 'wpcalibrate-dynamic-date-time' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'When enabled, shortcodes with live="yes" update every second without page reloads using a centralized performance-optimized timer.', 'wpcalibrate-dynamic-date-time' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<?php esc_html_e( 'Data Removal on Uninstall', 'wpcalibrate-dynamic-date-time' ); ?>
					</th>
					<td>
						<label for="delete_data_on_uninstall">
							<input type="checkbox" id="delete_data_on_uninstall" name="settings[delete_data_on_uninstall]" value="1" <?php checked( ! empty( $settings['delete_data_on_uninstall'] ) ); ?> />
							<?php esc_html_e( 'Delete plugin settings and presets when uninstalling', 'wpcalibrate-dynamic-date-time' ); ?>
						</label>
						<p class="description">
							<?php esc_html_e( 'Default is OFF. If unchecked, your settings and presets are preserved when uninstalling the plugin.', 'wpcalibrate-dynamic-date-time' ); ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'wpcalibrate-dynamic-date-time' ); ?></button>
		</p>
	</form>
</div>
