<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package WPCalibrate\DynamicDateTime
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Fetch plugin settings.
$wpcalibrate_ddt_settings = get_option( 'wpcalibrate_ddt_settings', [] );

// Only clean up options if the administrator opted in to data removal on uninstall.
if ( is_array( $wpcalibrate_ddt_settings ) && ! empty( $wpcalibrate_ddt_settings['delete_data_on_uninstall'] ) ) {
	delete_option( 'wpcalibrate_ddt_settings' );
	delete_option( 'wpcalibrate_ddt_presets' );
	delete_option( 'wpcalibrate_ddt_version' );
}
