<?php
/**
 * Fired during plugin deactivation.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Deactivator
 *
 * Handles clean deactivation without deleting configuration or user presets.
 */
class Deactivator {

	/**
	 * Run deactivation routine.
	 */
	public static function deactivate(): void {
		// Do not delete settings or presets.
		// Temporary transients or caches can be cleared here if any are added.
	}
}
