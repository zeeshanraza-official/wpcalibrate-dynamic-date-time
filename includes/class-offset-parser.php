<?php
/**
 * Safe offset parser for relative date offsets.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OffsetParser
 *
 * Enforces a strict grammar for day offsets and limits them safely (+/- 3650 days).
 */
class OffsetParser {

	/**
	 * Maximum allowed offset in days (approx 10 years).
	 */
	public const MAX_DAYS = 3650;

	/**
	 * Minimum allowed offset in days.
	 */
	public const MIN_DAYS = -3650;

	/**
	 * Parse an offset string into an integer day offset.
	 *
	 * @param string|null $offset Raw offset string from shortcode or block attribute.
	 * @return int Number of days offset, or 0 if empty or invalid.
	 */
	public static function parse( ?string $offset ): int {
		if ( null === $offset || '' === trim( $offset ) ) {
			return 0;
		}

		$trimmed = trim( strtolower( $offset ) );

		// Named aliases.
		if ( 'tomorrow' === $trimmed ) {
			return 1;
		}
		if ( 'yesterday' === $trimmed ) {
			return -1;
		}
		if ( 'today' === $trimmed || 'none' === $trimmed || '0' === $trimmed ) {
			return 0;
		}

		// Strictly match [+-]?\d+ day(s)
		if ( preg_match( '/^\s*([+-]?\d+)\s*days?\s*$/i', $trimmed, $matches ) ) {
			$days = (int) $matches[1];

			if ( $days > self::MAX_DAYS ) {
				return self::MAX_DAYS;
			}
			if ( $days < self::MIN_DAYS ) {
				return self::MIN_DAYS;
			}

			return $days;
		}

		// Reject any other arbitrary expression safely.
		return 0;
	}

	/**
	 * Determine if an offset string matches the allowed grammar.
	 *
	 * @param string|null $offset Raw offset string.
	 * @return bool True if valid, false otherwise.
	 */
	public static function is_valid( ?string $offset ): bool {
		if ( null === $offset || '' === trim( $offset ) ) {
			return true;
		}

		$trimmed = trim( strtolower( $offset ) );
		if ( in_array( $trimmed, [ 'tomorrow', 'yesterday', 'today', 'none', '0' ], true ) ) {
			return true;
		}

		if ( preg_match( '/^\s*([+-]?\d+)\s*days?\s*$/i', $trimmed, $matches ) ) {
			$days = (int) $matches[1];
			return $days >= self::MIN_DAYS && $days <= self::MAX_DAYS;
		}

		return false;
	}

	/**
	 * Format an integer day offset into standard string format.
	 *
	 * @param int $days Number of days.
	 * @return string Formatted string like "+3 days", "-1 day", "0 days".
	 */
	public static function format_days( int $days ): string {
		if ( 0 === $days ) {
			return '0 days';
		}
		$abs  = abs( $days );
		$unit = 1 === $abs ? 'day' : 'days';
		$sign = $days > 0 ? '+' : '-';
		return sprintf( '%s%d %s', $sign, $abs, $unit );
	}
}
