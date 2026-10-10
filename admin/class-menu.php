<?php
/**
 * Admin menu management class.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Menu
 *
 * Handles creation or reuse of the WPCalibrate top-level dashboard menu
 * and registers the Dynamic Date & Time submenu.
 */
class Menu {

	/**
	 * Top-level parent menu slug.
	 */
	public const PARENT_SLUG = 'wpcalibrate';

	/**
	 * Submenu page slug.
	 */
	public const PAGE_SLUG = 'wpcalibrate-dynamic-date-time';

	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	private Settings $settings;

	/**
	 * Constructor.
	 *
	 * @param Settings $settings Settings instance.
	 */
	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Initialize menu hook.
	 */
	public function init(): void {
		// Priority 60 allows other plugins or base loader to register first if present,
		// or if priority 9/10, we inspect existing menus cleanly.
		add_action( 'admin_menu', [ $this, 'register_menu' ], 60 );
		add_action( 'admin_head', [ $this, 'add_menu_icon_styles' ] );
	}

	/**
	 * Check if the WPCalibrate parent menu has already been registered.
	 *
	 * @return bool
	 */
	public static function has_parent_menu(): bool {
		global $menu;
		if ( ! is_array( $menu ) ) {
			return false;
		}

		foreach ( $menu as $item ) {
			if ( isset( $item[2] ) && self::PARENT_SLUG === $item[2] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Locate the best branding menu icon URL according to background contrast.
	 *
	 * WordPress admin sidebar has a dark background by default, so icon-white provides high contrast.
	 *
	 * @param string $contrast 'white' or 'dark'.
	 * @return string|null Icon URL if found, null otherwise.
	 */
	public static function get_branding_icon_url( string $contrast = 'white' ): ?string {
		$allowed_extensions = [ 'png', 'svg', 'webp', 'jpg' ];
		$base_dir           = WPCALIBRATE_DDT_DIR . 'branding/';
		$base_url           = WPCALIBRATE_DDT_URL . 'branding/';
		$filename_prefix    = 'icon-' . $contrast;

		foreach ( $allowed_extensions as $ext ) {
			$file_path = $base_dir . $filename_prefix . '.' . $ext;
			if ( file_exists( $file_path ) ) {
				return $base_url . $filename_prefix . '.' . $ext;
			}
		}

		// Fall back to opposite contrast if primary not found.
		$alt_contrast = 'white' === $contrast ? 'dark' : 'white';
		foreach ( $allowed_extensions as $ext ) {
			$file_path = $base_dir . 'icon-' . $alt_contrast . '.' . $ext;
			if ( file_exists( $file_path ) ) {
				return $base_url . 'icon-' . $alt_contrast . '.' . $ext;
			}
		}

		return null;
	}

	/**
	 * Register the WPCalibrate parent menu (if needed) and this plugin's submenu.
	 */
	public function register_menu(): void {
		$parent_exists = self::has_parent_menu();
		$menu_icon     = self::get_branding_icon_url( 'white' );

		// Fallback to native dashicon if branding icon is absent.
		if ( null === $menu_icon ) {
			$menu_icon = 'dashicons-calendar-alt';
		}

		if ( ! $parent_exists ) {
			// Register top-level WPCalibrate menu.
			add_menu_page(
				__( 'WPCalibrate', 'wpcalibrate-dynamic-date-time' ),
				__( 'WPCalibrate', 'wpcalibrate-dynamic-date-time' ),
				'manage_options',
				self::PARENT_SLUG,
				[ $this->settings, 'render_page' ],
				$menu_icon,
				65
			);

			// First submenu under our new parent: Overview (replaces default auto-generated parent entry).
			add_submenu_page(
				self::PARENT_SLUG,
				__( 'WPCalibrate Overview', 'wpcalibrate-dynamic-date-time' ),
				__( 'Overview', 'wpcalibrate-dynamic-date-time' ),
				'manage_options',
				self::PARENT_SLUG,
				[ $this->settings, 'render_page' ]
			);

			// Second submenu: Dynamic Date & Time.
			$hook = add_submenu_page(
				self::PARENT_SLUG,
				__( 'Dynamic Date & Time', 'wpcalibrate-dynamic-date-time' ),
				__( 'Dynamic Date & Time', 'wpcalibrate-dynamic-date-time' ),
				'manage_options',
				self::PAGE_SLUG,
				[ $this->settings, 'render_page' ]
			);
		} else {
			// Parent menu was already registered by another WPCalibrate plugin; add only our isolated submenu.
			$hook = add_submenu_page(
				self::PARENT_SLUG,
				__( 'Dynamic Date & Time', 'wpcalibrate-dynamic-date-time' ),
				__( 'Dynamic Date & Time', 'wpcalibrate-dynamic-date-time' ),
				'manage_options',
				self::PAGE_SLUG,
				[ $this->settings, 'render_page' ]
			);
		}

		if ( $hook ) {
			Assets::set_settings_page_hook( $hook );
		}
	}

	/**
	 * Output CSS in admin head to ensure the menu icon is never oversized in the sidebar.
	 */
	public function add_menu_icon_styles(): void {
		?>
		<style>
			#adminmenu .toplevel_page_wpcalibrate .wp-menu-image img,
			#adminmenu .toplevel_page_wpcalibrate-dynamic-date-time .wp-menu-image img {
				max-width: 20px;
				max-height: 20px;
				padding-top: 7px;
				object-fit: contain;
			}
		</style>
		<?php
	}
}
