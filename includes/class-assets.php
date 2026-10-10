<?php
/**
 * Asset management for admin and front-end scripts and styles.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Assets
 *
 * Registers and enqueues scripts/styles conditionally and efficiently.
 */
class Assets {

	/**
	 * Hook suffix for plugin settings page.
	 *
	 * @var string|null
	 */
	private static ?string $settings_page_hook = null;

	/**
	 * Set the settings page hook suffix.
	 *
	 * @param string $hook_suffix Hook suffix returned by add_submenu_page/add_menu_page.
	 */
	public static function set_settings_page_hook( string $hook_suffix ): void {
		self::$settings_page_hook = $hook_suffix;
	}

	/**
	 * Register actions for assets.
	 */
	public static function init(): void {
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'register_public_assets' ] );
		add_action( 'wp_footer', [ __CLASS__, 'maybe_enqueue_public_scripts' ], 5 );
		add_action( 'init', [ __CLASS__, 'register_block' ] );
	}

	/**
	 * Enqueue admin scripts and styles only on this plugin's settings page.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public static function enqueue_admin_assets( string $hook_suffix ): void {
		// Only load on our settings page or if hook suffix matches.
		$is_our_page = false;

		if ( null !== self::$settings_page_hook && $hook_suffix === self::$settings_page_hook ) {
			$is_our_page = true;
		} elseif ( isset( $_GET['page'] ) && 'wpcalibrate-dynamic-date-time' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$is_our_page = true;
		}

		if ( ! $is_our_page ) {
			return;
		}

		$suffix = is_rtl() ? '-rtl' : '';
		$css_file = 'admin' . $suffix . '.css';
		$css_path = WPCALIBRATE_DDT_DIR . 'assets/css/' . $css_file;
		$css_url  = WPCALIBRATE_DDT_URL . 'assets/css/' . $css_file;

		// Fallback to admin.css if RTL file does not exist.
		if ( ! file_exists( $css_path ) ) {
			$css_url = WPCALIBRATE_DDT_URL . 'assets/css/admin.css';
		}

		wp_enqueue_style(
			'wpcalibrate-ddt-admin',
			$css_url,
			[],
			WPCALIBRATE_DDT_VERSION
		);

		wp_enqueue_script(
			'wpcalibrate-ddt-admin',
			WPCALIBRATE_DDT_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			WPCALIBRATE_DDT_VERSION,
			true
		);

		// Localize admin script for copy confirmations and notices.
		wp_localize_script(
			'wpcalibrate-ddt-admin',
			'wpcalDdtAdmin',
			[
				'copied'        => __( 'Copied to clipboard!', 'wpcalibrate-dynamic-date-time' ),
				'confirmDelete' => __( 'Are you sure you want to delete this preset? This action cannot be undone.', 'wpcalibrate-dynamic-date-time' ),
			]
		);
	}

	/**
	 * Register front-end scripts and styles.
	 */
	public static function register_public_assets(): void {
		wp_register_style(
			'wpcalibrate-ddt-public',
			WPCALIBRATE_DDT_URL . 'assets/css/public.css',
			[],
			WPCALIBRATE_DDT_VERSION
		);

		wp_register_script(
			'wpcalibrate-ddt-public',
			WPCALIBRATE_DDT_URL . 'assets/js/public.js',
			[],
			WPCALIBRATE_DDT_VERSION,
			true
		);

		$site_tz = function_exists( 'wp_timezone_string' ) ? wp_timezone_string() : 'UTC';

		wp_localize_script(
			'wpcalibrate-ddt-public',
			'wpcalDdtSettings',
			[
				'siteTimezone' => $site_tz,
				'siteLocale'   => determine_locale(),
			]
		);
	}

	/**
	 * Conditionally enqueue public JavaScript in the footer if a dynamic element was rendered.
	 */
	public static function maybe_enqueue_public_scripts(): void {
		if ( DateTimeService::needs_front_script() ) {
			wp_enqueue_script( 'wpcalibrate-ddt-public' );
		}
	}

	/**
	 * Register Gutenberg block.
	 */
	public static function register_block(): void {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Register block editor script.
		wp_register_script(
			'wpcalibrate-ddt-block-editor',
			WPCALIBRATE_DDT_URL . 'blocks/dynamic-date-time/index.js',
			[ 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render', 'wp-i18n' ],
			WPCALIBRATE_DDT_VERSION,
			true
		);

		// Register block editor style.
		wp_register_style(
			'wpcalibrate-ddt-block-editor-style',
			WPCALIBRATE_DDT_URL . 'blocks/dynamic-date-time/editor.css',
			[ 'wp-edit-blocks' ],
			WPCALIBRATE_DDT_VERSION
		);

		// Pass preset options and defaults to Gutenberg block editor.
		$presets = PresetRepository::get_all();
		$preset_options = [
			[ 'value' => '', 'label' => __( '-- Select a Preset (Optional) --', 'wpcalibrate-dynamic-date-time' ) ],
		];
		foreach ( $presets as $p ) {
			$preset_options[] = [
				'value' => $p['slug'],
				'label' => sprintf( '%s (%s)', $p['name'], $p['slug'] ),
			];
		}

		wp_localize_script(
			'wpcalibrate-ddt-block-editor',
			'wpcalBlockData',
			[
				'presets' => $preset_options,
				'types'   => [
					[ 'value' => 'date', 'label' => __( 'Current Date', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'time', 'label' => __( 'Current Time', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'datetime', 'label' => __( 'Date and Time', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'day', 'label' => __( 'Day Name', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'day_num', 'label' => __( 'Day Number', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'month', 'label' => __( 'Month Name', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'month_num', 'label' => __( 'Month Number', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'year', 'label' => __( 'Year', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'tomorrow', 'label' => __( 'Tomorrow', 'wpcalibrate-dynamic-date-time' ) ],
					[ 'value' => 'yesterday', 'label' => __( 'Yesterday', 'wpcalibrate-dynamic-date-time' ) ],
				],
			]
		);

		// Register block type using block.json.
		$block_json = WPCALIBRATE_DDT_DIR . 'blocks/dynamic-date-time/block.json';
		if ( file_exists( $block_json ) ) {
			register_block_type_from_metadata(
				$block_json,
				[
					'render_callback' => [ __CLASS__, 'render_block' ],
				]
			);
		}
	}

	/**
	 * Server-side render callback for Gutenberg block.
	 *
	 * Consumes the exact same PHP DateTimeService as the shortcodes!
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string Rendered HTML.
	 */
	public static function render_block( array $attributes ): string {
		$atts = [
			'type'     => $attributes['type'] ?? 'date',
			'format'   => $attributes['format'] ?? '',
			'timezone' => $attributes['timezone'] ?? 'site',
			'offset'   => $attributes['offset'] ?? '',
			'live'     => ! empty( $attributes['live'] ) ? 'yes' : 'no',
			'preset'   => $attributes['preset'] ?? '',
			'class'    => $attributes['className'] ?? '',
		];

		return DateTimeService::render( $atts );
	}
}
