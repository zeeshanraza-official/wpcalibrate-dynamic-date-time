<?php
/**
 * Safe date and time format validator and sanitizer.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FormatValidator
 *
 * Validates, sanitizes, and provides default date/time formats.
 */
class FormatValidator {

	/**
	 * Default format strings mapped to output type.
	 */
	public const DEFAULT_FORMATS = [
		'date'       => 'F j, Y',
		'time'       => 'g:i A',
		'datetime'   => 'F j, Y g:i A',
		'day'        => 'l',
		'day_name'   => 'l',
		'day_num'    => 'j',
		'month'      => 'F',
		'month_name' => 'F',
		'month_num'  => 'm',
		'year'       => 'Y',
		'tomorrow'   => 'F j, Y',
		'yesterday'  => 'F j, Y',
	];

	/**
	 * Get the default format for a given output type.
	 *
	 * @param string               $type            Output type.
	 * @param array<string, mixed> $custom_defaults Optional array of custom defaults from settings.
	 * @return string Default date format string.
	 */
	public static function get_default_format( string $type, array $custom_defaults = [] ): string {
		$type = strtolower( trim( $type ) );

		if ( 'date' === $type && ! empty( $custom_defaults['default_date_format'] ) ) {
			return (string) $custom_defaults['default_date_format'];
		}
		if ( 'time' === $type && ! empty( $custom_defaults['default_time_format'] ) ) {
			return (string) $custom_defaults['default_time_format'];
		}
		if ( 'datetime' === $type && ! empty( $custom_defaults['default_datetime_format'] ) ) {
			return (string) $custom_defaults['default_datetime_format'];
		}

		return self::DEFAULT_FORMATS[ $type ] ?? 'F j, Y';
	}

	/**
	 * Sanitize and validate a date format string.
	 *
	 * @param string|null          $format          Input format string.
	 * @param string               $type            Output type to fall back to.
	 * @param array<string, mixed> $custom_defaults Optional settings array.
	 * @return string Sanitized and safe format string.
	 */
	public static function validate( ?string $format, string $type = 'date', array $custom_defaults = [] ): string {
		if ( null === $format || '' === trim( $format ) ) {
			return self::get_default_format( $type, $custom_defaults );
		}

		$raw = trim( $format );

		// Strip any HTML tags or null bytes.
		$clean = wp_strip_all_tags( $raw );
		$clean = str_replace( [ "\0", "\r", "\n" ], '', $clean );

		// Disallow dangerous characters that could attempt code or injection: <, >, $, `, ;, {, }
		$clean = (string) preg_replace( '/[<>$`{};]/', '', $clean );

		$clean = trim( $clean );

		if ( '' === $clean ) {
			return self::get_default_format( $type, $custom_defaults );
		}

		return $clean;
	}

	/**
	 * Check if a date format string requires second-by-second live updates.
	 *
	 * Unescaped 's', 'u', or 'v' tokens represent seconds or microseconds.
	 *
	 * @param string $format Format string.
	 * @return bool True if seconds are displayed.
	 */
	public static function contains_seconds( string $format ): bool {
		$len        = strlen( $format );
		$is_escaped = false;

		for ( $i = 0; $i < $len; $i++ ) {
			$char = $format[ $i ];

			if ( '\\' === $char ) {
				$is_escaped = ! $is_escaped;
				continue;
			}

			if ( ! $is_escaped && in_array( $char, [ 's', 'u', 'v' ], true ) ) {
				return true;
			}

			$is_escaped = false;
		}

		return false;
	}
}
