<?php
/**
 * Fired during plugin activation.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activator
 *
 * Handles plugin activation, schema version storage, and default option population.
 */
class Activator {

	/**
	 * Default plugin settings.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_default_settings(): array {
		return [
			'default_date_format'         => 'F j, Y',
			'default_time_format'         => 'g:i A',
			'default_datetime_format'     => 'F j, Y g:i A',
			'default_timezone_mode'       => 'site', // 'site' or 'visitor'
			'enable_visitor_timezone'     => 1,      // 1 or 0
			'enable_live_time'            => 1,      // 1 or 0
			'delete_data_on_uninstall'    => 0,      // 0 = preserve data, 1 = delete data
		];
	}

	/**
	 * Default presets created on clean install.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_default_presets(): array {
		return [
			'offer-expiry' => [
				'name'     => 'Offer Expiry',
				'slug'     => 'offer-expiry',
				'type'     => 'date',
				'format'   => 'l, F j',
				'timezone' => 'visitor',
				'offset'   => '+3 days',
				'live'     => 'no',
			],
			'live-time'    => [
				'name'     => 'Live Visitor Time',
				'slug'     => 'live-time',
				'type'     => 'time',
				'format'   => 'g:i:s A',
				'timezone' => 'visitor',
				'offset'   => '0 days',
				'live'     => 'yes',
			],
		];
	}

	/**
	 * Run activation routine.
	 */
	public static function activate(): void {
		// Initialize settings if they do not exist.
		$existing_settings = get_option( 'wpcalibrate_ddt_settings', null );
		if ( null === $existing_settings || ! is_array( $existing_settings ) ) {
			add_option( 'wpcalibrate_ddt_settings', self::get_default_settings(), '', 'yes' );
		}

		// Initialize presets if they do not exist.
		$existing_presets = get_option( 'wpcalibrate_ddt_presets', null );
		if ( null === $existing_presets || ! is_array( $existing_presets ) ) {
			add_option( 'wpcalibrate_ddt_presets', self::get_default_presets(), '', 'yes' );
		}

		// Save/update plugin schema version.
		update_option( 'wpcalibrate_ddt_version', WPCALIBRATE_DDT_VERSION, 'no' );
	}
}
