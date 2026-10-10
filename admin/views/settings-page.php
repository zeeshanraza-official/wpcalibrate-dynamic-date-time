<?php
/**
 * Main settings page view template.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$logo_url = Menu::get_branding_icon_url( 'dark' );
?>
<div class="wrap wpcal-admin-wrap">
	<header class="wpcal-admin-header">
		<div class="wpcal-header-branding">
			<?php if ( $logo_url ) : ?>
				<div class="wpcal-header-logo-wrap">
					<img src="<?php echo esc_url( $logo_url ); ?>" alt="WPCalibrate" class="wpcal-header-logo" width="44" height="44" />
				</div>
			<?php endif; ?>
			<div class="wpcal-header-title">
				<div class="wpcal-title-row">
					<h1><?php esc_html_e( 'WPCalibrate Dynamic Date & Time', 'wpcalibrate-dynamic-date-time' ); ?></h1>
					<span class="wpcal-header-badge"><?php printf( 'v%s', esc_html( WPCALIBRATE_DDT_VERSION ) ); ?></span>
					<span class="wpcal-status-pill"><span class="wpcal-pill-dot"></span> <?php esc_html_e( 'Ready', 'wpcalibrate-dynamic-date-time' ); ?></span>
				</div>
				<p class="wpcal-header-tagline">
					<?php esc_html_e( 'Display dynamic dates and times across landing pages, Gutenberg, Elementor, and shortcode builders.', 'wpcalibrate-dynamic-date-time' ); ?>
				</p>
			</div>
		</div>
	</header>

	<?php if ( 'settings_saved' === $message_code ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Settings saved successfully.', 'wpcalibrate-dynamic-date-time' ); ?></p>
		</div>
	<?php elseif ( 'preset_saved' === $message_code ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Preset saved successfully.', 'wpcalibrate-dynamic-date-time' ); ?></p>
		</div>
	<?php elseif ( 'preset_deleted' === $message_code ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Preset deleted successfully.', 'wpcalibrate-dynamic-date-time' ); ?></p>
		</div>
	<?php elseif ( ! empty( $error_text ) ) : ?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_html( $error_text ); ?></p>
		</div>
	<?php endif; ?>

	<nav class="nav-tab-wrapper wpcal-nav-tab-wrapper" aria-label="<?php esc_attr_e( 'Plugin Settings Tabs', 'wpcalibrate-dynamic-date-time' ); ?>">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=overview' ) ); ?>" class="nav-tab <?php echo 'overview' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-info"></span> <?php esc_html_e( 'Overview', 'wpcalibrate-dynamic-date-time' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=general' ) ); ?>" class="nav-tab <?php echo 'general' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e( 'General Settings', 'wpcalibrate-dynamic-date-time' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=presets' ) ); ?>" class="nav-tab <?php echo 'presets' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-tag"></span> <?php esc_html_e( 'Presets', 'wpcalibrate-dynamic-date-time' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=shortcodes' ) ); ?>" class="nav-tab <?php echo 'shortcodes' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-editor-code"></span> <?php esc_html_e( 'Shortcode Guide', 'wpcalibrate-dynamic-date-time' ); ?>
		</a>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Menu::PAGE_SLUG . '&tab=support' ) ); ?>" class="nav-tab <?php echo 'support' === $current_tab ? 'nav-tab-active' : ''; ?>">
			<span class="dashicons dashicons-sos"></span> <?php esc_html_e( 'Support', 'wpcalibrate-dynamic-date-time' ); ?>
		</a>
	</nav>

	<div class="wpcal-tab-content">
		<?php
		switch ( $current_tab ) {
			case 'general':
				require WPCALIBRATE_DDT_DIR . 'admin/views/tab-general.php';
				break;
			case 'presets':
				require WPCALIBRATE_DDT_DIR . 'admin/views/tab-presets.php';
				break;
			case 'shortcodes':
				require WPCALIBRATE_DDT_DIR . 'admin/views/tab-shortcodes.php';
				break;
			case 'support':
				require WPCALIBRATE_DDT_DIR . 'admin/views/tab-support.php';
				break;
			case 'overview':
			default:
				require WPCALIBRATE_DDT_DIR . 'admin/views/tab-overview.php';
				break;
		}
		?>
	</div>
</div>
