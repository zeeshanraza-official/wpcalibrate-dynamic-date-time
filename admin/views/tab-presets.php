<?php
/**
 * Presets management tab view template.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edit_slug   = isset( $_GET['edit'] ) ? sanitize_key( (string) $_GET['edit'] ) : null;
$edit_preset = $edit_slug ? PresetRepository::get( $edit_slug ) : null;
$is_editing  = null !== $edit_preset;
?>
<div class="wpcal-presets-container">
	<div class="wpcal-settings-card wpcal-presets-list-card">
		<div class="wpcal-card-header-flex">
			<div class="wpcal-card-header-titles">
				<h2><?php esc_html_e( 'Saved Reusable Presets', 'wpcalibrate-dynamic-date-time' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Presets bundle your date type, format, timezone, offset, and live update rules into a single reusable identifier.', 'wpcalibrate-dynamic-date-time' ); ?>
				</p>
			</div>
			<?php if ( ! $is_editing ) : ?>
				<a href="#preset-form-section" class="button button-primary wpcal-add-preset-btn">
					<span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Add New Preset', 'wpcalibrate-dynamic-date-time' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( empty( $presets ) ) : ?>
			<div class="wpcal-empty-state">
				<span class="dashicons dashicons-tag"></span>
				<p><?php esc_html_e( 'No presets found. Create your first preset using the form below.', 'wpcalibrate-dynamic-date-time' ); ?></p>
			</div>
		<?php else : ?>
			<div class="wpcal-table-responsive">
				<table class="wp-list-table widefat fixed striped wpcal-presets-table">
					<thead>
						<tr>
							<th scope="col" class="col-preset-identity"><?php esc_html_e( 'Preset & Slug', 'wpcalibrate-dynamic-date-time' ); ?></th>
							<th scope="col" class="col-type"><?php esc_html_e( 'Type', 'wpcalibrate-dynamic-date-time' ); ?></th>
							<th scope="col" class="col-format"><?php esc_html_e( 'Format', 'wpcalibrate-dynamic-date-time' ); ?></th>
							<th scope="col" class="col-tz"><?php esc_html_e( 'Timezone', 'wpcalibrate-dynamic-date-time' ); ?></th>
							<th scope="col" class="col-offset"><?php esc_html_e( 'Offset', 'wpcalibrate-dynamic-date-time' ); ?></th>
							<th scope="col" class="col-live"><?php esc_html_e( 'Live', 'wpcalibrate-dynamic-date-time' ); ?></th>
							<th scope="col" class="col-snippet"><?php esc_html_e( 'Shortcode Example', 'wpcalibrate-dynamic-date-time' ); ?></th>
							<th scope="col" class="col-actions"><?php esc_html_e( 'Actions', 'wpcalibrate-dynamic-date-time' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $presets as $slug => $p ) : ?>
							<?php
							$shortcode_snippet = sprintf( '[wpcal_datetime preset="%s"]', esc_attr( $p['slug'] ) );
							$delete_url        = wp_nonce_url(
								add_query_arg(
									[
										'page'        => Menu::PAGE_SLUG,
										'tab'         => 'presets',
										'action'      => 'delete_preset',
										'preset_slug' => $p['slug'],
									],
									admin_url( 'admin.php' )
								),
								'wpcal_ddt_delete_' . $p['slug']
							);
							$edit_url          = add_query_arg(
								[
									'page' => Menu::PAGE_SLUG,
									'tab'  => 'presets',
									'edit' => $p['slug'],
								],
								admin_url( 'admin.php' )
							);

							$type_class = match ( $p['type'] ) {
								'date' => 'wpcal-badge-primary',
								'time' => 'wpcal-badge-purple',
								'datetime' => 'wpcal-badge-teal',
								default => 'wpcal-badge-info',
							};
							?>
							<tr>
								<td class="col-preset-identity">
									<div class="wpcal-preset-cell">
										<strong class="wpcal-preset-title"><?php echo esc_html( $p['name'] ); ?></strong>
										<span class="wpcal-slug-pill"><code><?php echo esc_html( $p['slug'] ); ?></code></span>
									</div>
								</td>
								<td class="col-type">
									<span class="wpcal-badge <?php echo esc_attr( $type_class ); ?>"><?php echo esc_html( $p['type'] ); ?></span>
								</td>
								<td class="col-format">
									<code class="wpcal-format-code"><?php echo esc_html( $p['format'] ); ?></code>
								</td>
								<td class="col-tz">
									<?php if ( 'visitor' === $p['timezone'] ) : ?>
										<span class="wpcal-badge wpcal-badge-visitor"><span class="dashicons dashicons-location"></span> <?php esc_html_e( 'Visitor', 'wpcalibrate-dynamic-date-time' ); ?></span>
									<?php else : ?>
										<span class="wpcal-badge wpcal-badge-site"><span class="dashicons dashicons-admin-site-alt3"></span> <?php esc_html_e( 'Site', 'wpcalibrate-dynamic-date-time' ); ?></span>
									<?php endif; ?>
								</td>
								<td class="col-offset">
									<span class="wpcal-offset-pill"><?php echo esc_html( $p['offset'] ); ?></span>
								</td>
								<td class="col-live">
									<?php if ( 'yes' === $p['live'] ) : ?>
										<span class="wpcal-live-indicator" title="<?php esc_attr_e( 'Live updating active', 'wpcalibrate-dynamic-date-time' ); ?>">
											<span class="wpcal-live-dot"></span> <?php esc_html_e( 'Live', 'wpcalibrate-dynamic-date-time' ); ?>
										</span>
									<?php else : ?>
										<span class="wpcal-static-dash" title="<?php esc_attr_e( 'Static output', 'wpcalibrate-dynamic-date-time' ); ?>">&mdash;</span>
									<?php endif; ?>
								</td>
								<td class="col-snippet">
									<div class="wpcal-snippet-wrap">
										<code class="wpcal-snippet-code" title="<?php echo esc_attr( $shortcode_snippet ); ?>"><?php echo esc_html( $shortcode_snippet ); ?></code>
										<button type="button" class="wpcal-copy-icon-btn" data-clipboard="<?php echo esc_attr( $shortcode_snippet ); ?>" title="<?php esc_attr_e( 'Copy shortcode', 'wpcalibrate-dynamic-date-time' ); ?>">
											<span class="dashicons dashicons-admin-page"></span>
											<span class="wpcal-copy-label"><?php esc_html_e( 'Copy', 'wpcalibrate-dynamic-date-time' ); ?></span>
										</button>
									</div>
								</td>
								<td class="col-actions">
									<div class="wpcal-row-actions">
										<a href="<?php echo esc_url( $edit_url ); ?>" class="button button-small wpcal-action-btn" title="<?php esc_attr_e( 'Edit preset', 'wpcalibrate-dynamic-date-time' ); ?>">
											<span class="dashicons dashicons-edit"></span> <?php esc_html_e( 'Edit', 'wpcalibrate-dynamic-date-time' ); ?>
										</a>
										<a href="<?php echo esc_url( $delete_url ); ?>" class="button button-small button-link-delete wpcal-action-btn wpcal-delete-btn" title="<?php esc_attr_e( 'Delete preset', 'wpcalibrate-dynamic-date-time' ); ?>" onclick="return confirm(wpcalDdtAdmin.confirmDelete);">
											<span class="dashicons dashicons-trash"></span> <?php esc_html_e( 'Delete', 'wpcalibrate-dynamic-date-time' ); ?>
										</a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>

	<div class="wpcal-settings-card wpcal-preset-form-card" id="preset-form-section">
		<div class="wpcal-card-header">
			<h2>
				<?php
				if ( $is_editing ) {
					printf(
						/* translators: %s: Preset name */
						esc_html__( 'Edit Preset: %s', 'wpcalibrate-dynamic-date-time' ),
						esc_html( $edit_preset['name'] )
					);
				} else {
					esc_html_e( 'Add New Date/Time Preset', 'wpcalibrate-dynamic-date-time' );
				}
				?>
			</h2>
			<p class="description">
				<?php esc_html_e( 'Define reusable formatting and timezone logic. Once saved, reference this preset anywhere via shortcode or Gutenberg block.', 'wpcalibrate-dynamic-date-time' ); ?>
			</p>
		</div>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=presets' ) ); ?>" class="wpcal-form wpcal-grid-form">
			<?php wp_nonce_field( 'wpcal_ddt_save_preset', 'wpcal_ddt_nonce' ); ?>
			<input type="hidden" name="wpcal_ddt_action" value="save_preset" />
			<?php if ( $is_editing ) : ?>
				<input type="hidden" name="existing_slug" value="<?php echo esc_attr( $edit_preset['slug'] ); ?>" />
			<?php endif; ?>

			<div class="wpcal-form-grid">
				<!-- Row 1: Name & Slug -->
				<div class="wpcal-field-group">
					<label for="preset_name" class="wpcal-field-label">
						<?php esc_html_e( 'Preset Name', 'wpcalibrate-dynamic-date-time' ); ?> <span class="required">*</span>
					</label>
					<input type="text" id="preset_name" name="preset[name]" value="<?php echo esc_attr( $is_editing ? $edit_preset['name'] : '' ); ?>" class="regular-text wpcal-input" required placeholder="<?php esc_attr_e( 'e.g. Flash Sale Deadline', 'wpcalibrate-dynamic-date-time' ); ?>" />
					<p class="wpcal-help-text"><?php esc_html_e( 'Human-readable label shown in presets list and Gutenberg block dropdown.', 'wpcalibrate-dynamic-date-time' ); ?></p>
				</div>

				<div class="wpcal-field-group">
					<label for="preset_slug" class="wpcal-field-label">
						<?php esc_html_e( 'Unique Preset Slug', 'wpcalibrate-dynamic-date-time' ); ?> <span class="required">*</span>
					</label>
					<input type="text" id="preset_slug" name="preset[slug]" value="<?php echo esc_attr( $is_editing ? $edit_preset['slug'] : '' ); ?>" class="regular-text code wpcal-input" required placeholder="<?php esc_attr_e( 'e.g. flash-sale-deadline', 'wpcalibrate-dynamic-date-time' ); ?>" />
					<p class="wpcal-help-text"><?php esc_html_e( 'Unique identifier used in [wpcal_datetime preset="..."] (lowercase letters, numbers, hyphens).', 'wpcalibrate-dynamic-date-time' ); ?></p>
				</div>

				<!-- Row 2: Type & Timezone -->
				<div class="wpcal-field-group">
					<label for="preset_type" class="wpcal-field-label">
						<?php esc_html_e( 'Output Type', 'wpcalibrate-dynamic-date-time' ); ?>
					</label>
					<select id="preset_type" name="preset[type]" class="wpcal-select">
						<option value="date" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'date' ); ?>><?php esc_html_e( 'Current Date', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="time" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'time' ); ?>><?php esc_html_e( 'Current Time', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="datetime" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'datetime' ); ?>><?php esc_html_e( 'Date & Time', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="day" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'day' ); ?>><?php esc_html_e( 'Day Name (e.g. Monday)', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="day_num" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'day_num' ); ?>><?php esc_html_e( 'Day Number (e.g. 15)', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="month" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'month' ); ?>><?php esc_html_e( 'Month Name (e.g. October)', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="month_num" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'month_num' ); ?>><?php esc_html_e( 'Month Number (e.g. 10)', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="year" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'year' ); ?>><?php esc_html_e( 'Year (e.g. 2026)', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="tomorrow" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'tomorrow' ); ?>><?php esc_html_e( 'Tomorrow (+1 Day)', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="yesterday" <?php selected( $is_editing ? $edit_preset['type'] : 'date', 'yesterday' ); ?>><?php esc_html_e( 'Yesterday (-1 Day)', 'wpcalibrate-dynamic-date-time' ); ?></option>
					</select>
				</div>

				<div class="wpcal-field-group">
					<label for="preset_timezone" class="wpcal-field-label">
						<?php esc_html_e( 'Timezone Mode', 'wpcalibrate-dynamic-date-time' ); ?>
					</label>
					<select id="preset_timezone" name="preset[timezone]" class="wpcal-select">
						<option value="site" <?php selected( $is_editing ? $edit_preset['timezone'] : 'site', 'site' ); ?>><?php esc_html_e( 'WordPress Site Timezone', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="visitor" <?php selected( $is_editing ? $edit_preset['timezone'] : 'site', 'visitor' ); ?>><?php esc_html_e( 'Visitor Browser Timezone', 'wpcalibrate-dynamic-date-time' ); ?></option>
					</select>
				</div>

				<!-- Row 3: Format & Offset -->
				<div class="wpcal-field-group">
					<label for="preset_format" class="wpcal-field-label">
						<?php esc_html_e( 'Date / Time Format', 'wpcalibrate-dynamic-date-time' ); ?>
					</label>
					<input type="text" id="preset_format" name="preset[format]" value="<?php echo esc_attr( $is_editing ? $edit_preset['format'] : 'F j, Y' ); ?>" class="regular-text code wpcal-input" />
					<p class="wpcal-help-text"><?php esc_html_e( 'Standard PHP date tokens (e.g. F j, Y, g:i A, l, F j, d/m/Y).', 'wpcalibrate-dynamic-date-time' ); ?></p>
				</div>

				<div class="wpcal-field-group">
					<label for="preset_offset" class="wpcal-field-label">
						<?php esc_html_e( 'Relative Day Offset', 'wpcalibrate-dynamic-date-time' ); ?>
					</label>
					<input type="text" id="preset_offset" name="preset[offset]" value="<?php echo esc_attr( $is_editing ? $edit_preset['offset'] : '0 days' ); ?>" class="regular-text code wpcal-input" placeholder="<?php esc_attr_e( 'e.g. +3 days or -1 day', 'wpcalibrate-dynamic-date-time' ); ?>" />
					<p class="wpcal-help-text"><?php esc_html_e( 'Safe offset format: +X days or -X days (e.g. +1 day, +7 days, -2 days, 0 days).', 'wpcalibrate-dynamic-date-time' ); ?></p>
				</div>

				<!-- Row 4: Live Clock Toggle -->
				<div class="wpcal-field-group wpcal-field-full">
					<label for="preset_live" class="wpcal-field-label">
						<?php esc_html_e( 'Live Clock Mode', 'wpcalibrate-dynamic-date-time' ); ?>
					</label>
					<select id="preset_live" name="preset[live]" class="wpcal-select">
						<option value="no" <?php selected( $is_editing ? $edit_preset['live'] : 'no', 'no' ); ?>><?php esc_html_e( 'No (Static text output)', 'wpcalibrate-dynamic-date-time' ); ?></option>
						<option value="yes" <?php selected( $is_editing ? $edit_preset['live'] : 'no', 'yes' ); ?>><?php esc_html_e( 'Yes (Updates every second without page reload)', 'wpcalibrate-dynamic-date-time' ); ?></option>
					</select>
				</div>
			</div>

			<p class="submit wpcal-form-actions">
				<button type="submit" class="button button-primary button-hero">
					<?php echo $is_editing ? esc_html__( 'Update Preset', 'wpcalibrate-dynamic-date-time' ) : esc_html__( 'Save Preset', 'wpcalibrate-dynamic-date-time' ); ?>
				</button>
				<?php if ( $is_editing ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=presets' ) ); ?>" class="button button-secondary">
						<?php esc_html_e( 'Cancel', 'wpcalibrate-dynamic-date-time' ); ?>
					</a>
				<?php endif; ?>
			</p>
		</form>
	</div>
</div>
