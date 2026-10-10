<?php
/**
 * Shortcode handlers for WPCalibrate Dynamic Date & Time.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Shortcodes
 *
 * Registers and handles [wpcal_datetime], [wpcal_date], and [wpcal_time].
 */
class Shortcodes {

	/**
	 * Register shortcode tags with WordPress.
	 */
	public static function register(): void {
		add_shortcode( 'wpcal_datetime', [ __CLASS__, 'handle_datetime' ] );
		add_shortcode( 'wpcal_date', [ __CLASS__, 'handle_date' ] );
		add_shortcode( 'wpcal_time', [ __CLASS__, 'handle_time' ] );
	}

	/**
	 * Handler for primary [wpcal_datetime] shortcode.
	 *
	 * @param array<string, mixed>|string $atts Shortcode attributes.
	 * @return string Rendered HTML.
	 */
	public static function handle_datetime( array|string $atts ): string {
		$raw_atts = is_array( $atts ) ? $atts : [];
		return DateTimeService::render( $raw_atts );
	}

	/**
	 * Handler for [wpcal_date] alias.
	 *
	 * @param array<string, mixed>|string $atts Shortcode attributes.
	 * @return string Rendered HTML.
	 */
	public static function handle_date( array|string $atts ): string {
		$raw_atts = is_array( $atts ) ? $atts : [];
		if ( empty( $raw_atts['type'] ) ) {
			$raw_atts['type'] = 'date';
		}
		return DateTimeService::render( $raw_atts );
	}

	/**
	 * Handler for [wpcal_time] alias.
	 *
	 * @param array<string, mixed>|string $atts Shortcode attributes.
	 * @return string Rendered HTML.
	 */
	public static function handle_time( array|string $atts ): string {
		$raw_atts = is_array( $atts ) ? $atts : [];
		if ( empty( $raw_atts['type'] ) ) {
			$raw_atts['type'] = 'time';
		}
		return DateTimeService::render( $raw_atts );
	}
}
