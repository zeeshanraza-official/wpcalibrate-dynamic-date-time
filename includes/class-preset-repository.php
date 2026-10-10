<?php
/**
 * Preset repository for managing reusable date/time presets.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PresetRepository
 *
 * Handles persistent storage, retrieval, validation, and deletion of presets.
 */
class PresetRepository {

	/**
	 * Option key for presets.
	 */
	public const OPTION_NAME = 'wpcalibrate_ddt_presets';

	/**
	 * Reserved slug names that cannot be used as preset identifiers.
	 */
	public const RESERVED_SLUGS = [
		'date',
		'time',
		'datetime',
		'default',
		'site',
		'visitor',
		'admin',
		'system',
		'all',
	];

	/**
	 * Allowed output types.
	 */
	public const ALLOWED_TYPES = [
		'date',
		'time',
		'datetime',
		'day',
		'day_name',
		'day_num',
		'month',
		'month_name',
		'month_num',
		'year',
		'tomorrow',
		'yesterday',
	];

	/**
	 * In-memory cache for presets within current request.
	 *
	 * @var array<string, array<string, mixed>>|null
	 */
	private static ?array $cache = null;

	/**
	 * Fetch all saved presets.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_all(): array {
		if ( null !== self::$cache ) {
			return self::$cache;
		}

		$presets = get_option( self::OPTION_NAME, [] );
		if ( ! is_array( $presets ) ) {
			$presets = [];
		}

		self::$cache = $presets;
		return self::$cache;
	}

	/**
	 * Fetch a single preset by slug.
	 *
	 * @param string $slug Sanitized preset slug.
	 * @return array<string, mixed>|null Preset data array or null if not found.
	 */
	public static function get( string $slug ): ?array {
		$sanitized_slug = self::sanitize_slug( $slug );
		$all            = self::get_all();

		return $all[ $sanitized_slug ] ?? null;
	}

	/**
	 * Sanitize preset slug to lowercase letters, numbers, and hyphens.
	 *
	 * @param string $raw Raw slug input.
	 * @return string
	 */
	public static function sanitize_slug( string $raw ): string {
		$slug = strtolower( trim( $raw ) );
		$slug = (string) preg_replace( '/[^a-z0-9_-]/', '-', $slug );
		$slug = (string) preg_replace( '/-+/', '-', $slug );
		return trim( $slug, '-' );
	}

	/**
	 * Check if a preset slug already exists.
	 *
	 * @param string $slug Sanitized preset slug.
	 * @return bool
	 */
	public static function slug_exists( string $slug ): bool {
		$all = self::get_all();
		return isset( $all[ self::sanitize_slug( $slug ) ] );
	}

	/**
	 * Validate and sanitize a preset payload.
	 *
	 * @param array<string, mixed> $data          Raw preset input data.
	 * @param string|null          $existing_slug Optional slug if editing an existing preset.
	 * @return array<string, mixed>|WP_Error Validated preset array or WP_Error on failure.
	 */
	public static function validate( array $data, ?string $existing_slug = null ): array|WP_Error {
		$name = isset( $data['name'] ) ? sanitize_text_field( (string) $data['name'] ) : '';
		if ( '' === $name ) {
			return new WP_Error( 'empty_name', __( 'Preset Name is required.', 'wpcalibrate-dynamic-date-time' ) );
		}

		$raw_slug = isset( $data['slug'] ) ? (string) $data['slug'] : '';
		$slug     = self::sanitize_slug( $raw_slug );

		if ( '' === $slug ) {
			$slug = self::sanitize_slug( $name );
		}

		if ( '' === $slug ) {
			return new WP_Error( 'invalid_slug', __( 'Preset Slug must contain letters, numbers, or hyphens.', 'wpcalibrate-dynamic-date-time' ) );
		}

		if ( in_array( $slug, self::RESERVED_SLUGS, true ) ) {
			return new WP_Error( 'reserved_slug', __( 'The chosen slug is a reserved system identifier. Please choose another.', 'wpcalibrate-dynamic-date-time' ) );
		}

		// Ensure uniqueness if slug changed or is new.
		if ( ( null === $existing_slug || $existing_slug !== $slug ) && self::slug_exists( $slug ) ) {
			return new WP_Error( 'duplicate_slug', __( 'A preset with this slug already exists. Slugs must be unique.', 'wpcalibrate-dynamic-date-time' ) );
		}

		// Validate Type.
		$type = isset( $data['type'] ) ? strtolower( trim( (string) $data['type'] ) ) : 'date';
		if ( ! in_array( $type, self::ALLOWED_TYPES, true ) ) {
			$type = 'date';
		}

		// Validate Format.
		$format = isset( $data['format'] ) ? FormatValidator::validate( (string) $data['format'], $type ) : FormatValidator::get_default_format( $type );

		// Validate Timezone.
		$timezone = isset( $data['timezone'] ) ? strtolower( trim( (string) $data['timezone'] ) ) : 'site';
		if ( ! in_array( $timezone, [ 'site', 'visitor' ], true ) ) {
			$timezone = 'site';
		}

		// Validate Offset.
		$offset_raw = isset( $data['offset'] ) ? (string) $data['offset'] : '0 days';
		$days       = OffsetParser::parse( $offset_raw );
		$offset     = OffsetParser::format_days( $days );

		// Validate Live.
		$live_raw = isset( $data['live'] ) ? strtolower( trim( (string) $data['live'] ) ) : 'no';
		$live     = in_array( $live_raw, [ '1', 'true', 'yes' ], true ) ? 'yes' : 'no';

		return [
			'name'     => $name,
			'slug'     => $slug,
			'type'     => $type,
			'format'   => $format,
			'timezone' => $timezone,
			'offset'   => $offset,
			'live'     => $live,
		];
	}

	/**
	 * Save a preset (insert or update).
	 *
	 * @param array<string, mixed> $data          Preset data.
	 * @param string|null          $existing_slug Optional slug if updating.
	 * @return bool|WP_Error True on success, WP_Error on failure.
	 */
	public static function save( array $data, ?string $existing_slug = null ): bool|WP_Error {
		$validated = self::validate( $data, $existing_slug );
		if ( is_wp_error( $validated ) ) {
			return $validated;
		}

		$all = self::get_all();

		// If existing slug is changed, remove the old key.
		if ( null !== $existing_slug && $existing_slug !== $validated['slug'] && isset( $all[ $existing_slug ] ) ) {
			unset( $all[ $existing_slug ] );
		}

		$all[ $validated['slug'] ] = $validated;
		self::$cache               = $all;

		return update_option( self::OPTION_NAME, $all );
	}

	/**
	 * Delete a preset by slug.
	 *
	 * @param string $slug Preset slug to delete.
	 * @return bool True if deleted or did not exist, false on error.
	 */
	public static function delete( string $slug ): bool {
		$sanitized_slug = self::sanitize_slug( $slug );
		$all            = self::get_all();

		if ( ! isset( $all[ $sanitized_slug ] ) ) {
			return true;
		}

		unset( $all[ $sanitized_slug ] );
		self::$cache = $all;

		return update_option( self::OPTION_NAME, $all );
	}
}
