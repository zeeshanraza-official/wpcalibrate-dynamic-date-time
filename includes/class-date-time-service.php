<?php
/**
 * Core date and time service.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

use DateTimeImmutable;
use DateTimeZone;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class DateTimeService
 *
 * Handles attribute resolution, timezone calculations, safe offsets,
 * localized WordPress date formatting, and HTML generation.
 */
class DateTimeService {

	/**
	 * Tracks whether client-side dynamic enhancements (visitor TZ or live mode) were used.
	 *
	 * @var bool
	 */
	private static bool $needs_front_script = false;

	/**
	 * Check if front script is required for current page request.
	 *
	 * @return bool
	 */
	public static function needs_front_script(): bool {
		return self::$needs_front_script;
	}

	/**
	 * Mark that front script should be loaded.
	 */
	public static function flag_script_needed(): void {
		self::$needs_front_script = true;
	}

	/**
	 * Resolve attributes according to strict precedence rules:
	 * 1. Explicit shortcode/block attribute
	 * 2. Referenced preset value
	 * 3. Plugin default settings
	 * 4. Safe hard-coded fallback
	 *
	 * @param array<string, mixed> $raw_atts Raw attributes supplied by shortcode or block.
	 * @return array<string, mixed> Resolved normalized attributes.
	 */
	public static function resolve_attributes( array $raw_atts ): array {
		$settings = get_option( 'wpcalibrate_ddt_settings', [] );
		if ( ! is_array( $settings ) ) {
			$settings = Activator::get_default_settings();
		}

		// 1. Check if a preset is requested.
		$preset_slug = ! empty( $raw_atts['preset'] ) ? sanitize_key( (string) $raw_atts['preset'] ) : null;
		$preset      = $preset_slug ? PresetRepository::get( $preset_slug ) : null;

		// 2. Resolve Type.
		$type = null;
		if ( ! empty( $raw_atts['type'] ) ) {
			$type = strtolower( trim( (string) $raw_atts['type'] ) );
		} elseif ( $preset && ! empty( $preset['type'] ) ) {
			$type = (string) $preset['type'];
		} else {
			$type = 'date';
		}

		if ( ! in_array( $type, PresetRepository::ALLOWED_TYPES, true ) ) {
			$type = 'date';
		}

		// 3. Resolve Timezone mode.
		$global_visitor_enabled = (bool) ( $settings['enable_visitor_timezone'] ?? true );
		$timezone               = null;

		if ( ! empty( $raw_atts['timezone'] ) ) {
			$timezone = strtolower( trim( (string) $raw_atts['timezone'] ) );
		} elseif ( $preset && ! empty( $preset['timezone'] ) ) {
			$timezone = (string) $preset['timezone'];
		} elseif ( ! empty( $settings['default_timezone_mode'] ) ) {
			$timezone = (string) $settings['default_timezone_mode'];
		} else {
			$timezone = 'site';
		}

		if ( ! in_array( $timezone, [ 'site', 'visitor' ], true ) ) {
			$timezone = 'site';
		}

		// If visitor timezone is disabled globally in settings, force 'site'.
		if ( 'visitor' === $timezone && ! $global_visitor_enabled ) {
			$timezone = 'site';
		}

		// 4. Resolve Offset.
		$offset_days = 0;
		if ( isset( $raw_atts['offset'] ) && '' !== trim( (string) $raw_atts['offset'] ) ) {
			$offset_days = OffsetParser::parse( (string) $raw_atts['offset'] );
		} elseif ( 'tomorrow' === $type ) {
			$offset_days = 1;
		} elseif ( 'yesterday' === $type ) {
			$offset_days = -1;
		} elseif ( $preset && isset( $preset['offset'] ) && '' !== trim( (string) $preset['offset'] ) ) {
			$offset_days = OffsetParser::parse( (string) $preset['offset'] );
		}

		// 5. Resolve Format.
		$format = null;
		if ( ! empty( $raw_atts['format'] ) ) {
			$format = FormatValidator::validate( (string) $raw_atts['format'], $type, $settings );
		} elseif ( $preset && ! empty( $preset['format'] ) ) {
			$format = FormatValidator::validate( (string) $preset['format'], $type, $settings );
		} else {
			$format = FormatValidator::get_default_format( $type, $settings );
		}

		// 6. Resolve Live mode.
		$global_live_enabled = (bool) ( $settings['enable_live_time'] ?? true );
		$live                = false;

		if ( isset( $raw_atts['live'] ) ) {
			$live_val = strtolower( trim( (string) $raw_atts['live'] ) );
			$live     = in_array( $live_val, [ 'yes', 'true', '1', 'on' ], true );
		} elseif ( $preset && isset( $preset['live'] ) ) {
			$live = 'yes' === $preset['live'];
		}

		if ( $live && ! $global_live_enabled ) {
			$live = false;
		}

		// 7. Resolve Class.
		$classes = [];
		if ( ! empty( $raw_atts['class'] ) ) {
			$raw_classes = preg_split( '/\s+/', trim( (string) $raw_atts['class'] ) );
			if ( is_array( $raw_classes ) ) {
				foreach ( $raw_classes as $cls ) {
					$clean_cls = sanitize_html_class( $cls );
					if ( '' !== $clean_cls ) {
						$classes[] = $clean_cls;
					}
				}
			}
		}

		$resolved = [
			'type'         => $type,
			'format'       => $format,
			'timezone'     => $timezone,
			'offset_days'  => $offset_days,
			'live'         => $live,
			'class'        => implode( ' ', $classes ),
			'preset'       => $preset_slug,
			'has_seconds'  => FormatValidator::contains_seconds( $format ),
		];

		/**
		 * Filter the resolved attributes.
		 *
		 * @param array<string, mixed> $resolved Normalized attributes.
		 * @param array<string, mixed> $raw_atts Original raw attributes.
		 */
		return (array) apply_filters( 'wpcalibrate_ddt_resolved_attributes', $resolved, $raw_atts );
	}

	/**
	 * Compute localized date string for the WordPress site timezone.
	 *
	 * Respects site timezone from Settings > General and localized site language.
	 *
	 * @param string $format      Format string.
	 * @param int    $offset_days Calendar day offset.
	 * @return string Formatted localized date string.
	 */
	public static function get_site_time_string( string $format, int $offset_days = 0 ): string {
		$site_tz = function_exists( 'wp_timezone' ) ? wp_timezone() : new DateTimeZone( 'UTC' );

		// Use current_datetime() when available to obtain DateTimeImmutable in site timezone.
		if ( function_exists( 'current_datetime' ) ) {
			$dt = current_datetime();
		} else {
			$dt = new DateTimeImmutable( 'now', $site_tz );
		}

		// Apply calendar-day offset in target site timezone.
		if ( 0 !== $offset_days ) {
			$modifier = sprintf( '%+d days', $offset_days );
			$dt       = $dt->modify( $modifier );
		}

		// wp_date() localizes month/day strings to WordPress site locale.
		if ( function_exists( 'wp_date' ) ) {
			return wp_date( $format, $dt->getTimestamp(), $site_tz );
		}

		return $dt->format( $format );
	}

	/**
	 * Render the dynamic date/time HTML output.
	 *
	 * @param array<string, mixed> $atts Shortcode or block attributes.
	 * @return string Safe HTML string.
	 */
	public static function render( array $atts ): string {
		$config = self::resolve_attributes( $atts );

		// Generate server-rendered baseline text using site timezone.
		$fallback_text = self::get_site_time_string( $config['format'], $config['offset_days'] );

		$is_visitor = 'visitor' === $config['timezone'];
		$is_live    = (bool) $config['live'];

		// If neither visitor timezone nor live updating is required, output static text.
		if ( ! $is_visitor && ! $is_live ) {
			$classes = 'wpcal-datetime';
			if ( '' !== $config['class'] ) {
				$classes .= ' ' . $config['class'];
			}

			$output = sprintf(
				'<span class="%s">%s</span>',
				esc_attr( $classes ),
				esc_html( $fallback_text )
			);

			return (string) apply_filters( 'wpcalibrate_ddt_formatted_output', $output, $config );
		}

		// Otherwise, flag that JavaScript runtime is required.
		self::flag_script_needed();

		$classes = 'wpcal-datetime';
		if ( $is_live ) {
			$classes .= ' wpcal-live';
		}
		if ( $is_visitor ) {
			$classes .= ' wpcal-visitor';
		}
		if ( '' !== $config['class'] ) {
			$classes .= ' ' . $config['class'];
		}

		// Build data attributes for front-end enhancement.
		$data_attrs = [
			'data-wpcal-datetime' => 'true',
			'data-type'           => $config['type'],
			'data-format'         => $config['format'],
			'data-timezone'       => $config['timezone'],
			'data-offset'         => (string) $config['offset_days'],
			'data-live'           => $is_live ? '1' : '0',
			'data-has-seconds'    => $config['has_seconds'] ? '1' : '0',
		];

		$attr_strings = [];
		foreach ( $data_attrs as $key => $val ) {
			$attr_strings[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $val ) );
		}

		$output = sprintf(
			'<span class="%s" %s>%s</span>',
			esc_attr( $classes ),
			implode( ' ', $attr_strings ),
			esc_html( $fallback_text )
		);

		return (string) apply_filters( 'wpcalibrate_ddt_formatted_output', $output, $config );
	}
}
