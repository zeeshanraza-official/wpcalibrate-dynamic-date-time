<?php
/**
 * Plugin Name:       WPCalibrate Dynamic Date & Time
 * Plugin URI:        https://wpcalibrate.com
 * Description:       Display dynamic dates and times anywhere on your WordPress site—landing pages, Elementor, Gutenberg, and shortcode-ready page builders—with site/visitor timezones, offsets, and live updating.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      8.2
 * Author:            WPCalibrate
 * Author URI:        https://wpcalibrate.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpcalibrate-dynamic-date-time
 * Domain Path:       /languages
 * Update URI:        https://github.com/zeeshanraza-official/wpcalibrate-dynamic-date-time
 * GitHub Plugin URI: zeeshanraza-official/wpcalibrate-dynamic-date-time
 * Primary Branch:    main
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

use WPCalibrate\DynamicDateTime\Activator;
use WPCalibrate\DynamicDateTime\Deactivator;
use WPCalibrate\DynamicDateTime\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'WPCALIBRATE_DDT_VERSION', '1.0.1' );
define( 'WPCALIBRATE_DDT_FILE', __FILE__ );
define( 'WPCALIBRATE_DDT_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPCALIBRATE_DDT_URL', plugin_dir_url( __FILE__ ) );
define( 'WPCALIBRATE_DDT_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPCALIBRATE_DDT_MIN_PHP', '8.2' );

/**
 * Autoloader for WPCalibrate\DynamicDateTime namespace.
 *
 * @param string $class Class name.
 */
spl_autoload_register(
	static function ( string $class ): void {
		$prefix = 'WPCalibrate\\DynamicDateTime\\';
		if ( ! str_starts_with( $class, $prefix ) ) {
			return;
		}

		$relative_class = substr( $class, strlen( $prefix ) );
		$parts          = explode( '\\', $relative_class );
		$class_name     = array_pop( $parts );

		// Possible file name variations:
		// 1. Strict kebab-case: GitHubUpdater -> class-git-hub-updater.php
		$kebab_class = 'class-' . strtolower( (string) preg_replace( '/([a-z])([A-Z])/', '$1-$2', $class_name ) ) . '.php';

		// 2. Normalized without split (GitHub -> github): class-github-updater.php
		$normalized_class = str_replace( 'git-hub', 'github', $kebab_class );

		// 3. Simple lowercase hyphenated: class-githubupdater.php
		$plain_class = 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '.php';

		$candidates = array_unique( [ $kebab_class, $normalized_class, $plain_class ] );
		$directories = [ 'includes/', 'admin/', 'public/' ];

		foreach ( $directories as $dir ) {
			foreach ( $candidates as $candidate ) {
				$file = WPCALIBRATE_DDT_DIR . $dir . $sub_path . $candidate;
				if ( file_exists( $file ) ) {
					require_once $file;
					return;
				}
			}
		}
	}
);

// Require essential bootstrap files.
require_once WPCALIBRATE_DDT_DIR . 'includes/class-activator.php';
require_once WPCALIBRATE_DDT_DIR . 'includes/class-deactivator.php';
require_once WPCALIBRATE_DDT_DIR . 'includes/class-plugin.php';
require_once WPCALIBRATE_DDT_DIR . 'includes/class-git-hub-updater.php';

/**
 * Check PHP compatibility before running.
 *
 * @return bool
 */
function wpcalibrate_ddt_check_php_version(): bool {
	if ( version_compare( PHP_VERSION, WPCALIBRATE_DDT_MIN_PHP, '<' ) ) {
		add_action(
			'admin_notices',
			static function (): void {
				?>
				<div class="notice notice-error">
					<p>
						<?php
						printf(
							/* translators: 1: Plugin name, 2: Required PHP version, 3: Current PHP version */
							esc_html__( '%1$s requires PHP %2$s or higher. Your server is running PHP %3$s. Please update PHP to activate and use this plugin.', 'wpcalibrate-dynamic-date-time' ),
							'<strong>WPCalibrate Dynamic Date & Time</strong>',
							esc_html( WPCALIBRATE_DDT_MIN_PHP ),
							esc_html( PHP_VERSION )
						);
						?>
					</p>
				</div>
				<?php
			}
		);
		return false;
	}
	return true;
}

/**
 * Activation hook callback.
 */
function wpcalibrate_ddt_activate(): void {
	if ( ! wpcalibrate_ddt_check_php_version() ) {
		deactivate_plugins( WPCALIBRATE_DDT_BASENAME );
		wp_die(
			sprintf(
				/* translators: 1: Plugin name, 2: Required PHP version */
				esc_html__( '%1$s requires PHP %2$s or higher.', 'wpcalibrate-dynamic-date-time' ),
				'WPCalibrate Dynamic Date & Time',
				esc_html( WPCALIBRATE_DDT_MIN_PHP )
			)
		);
	}

	Activator::activate();
}
register_activation_hook( WPCALIBRATE_DDT_FILE, 'wpcalibrate_ddt_activate' );

/**
 * Deactivation hook callback.
 */
function wpcalibrate_ddt_deactivate(): void {
	Deactivator::deactivate();
}
register_deactivation_hook( WPCALIBRATE_DDT_FILE, 'wpcalibrate_ddt_deactivate' );

/**
 * Initialize the plugin after plugins are loaded.
 */
function wpcalibrate_ddt_init(): void {
	if ( ! wpcalibrate_ddt_check_php_version() ) {
		return;
	}

	// Load text domain.
	load_plugin_textdomain(
		'wpcalibrate-dynamic-date-time',
		false,
		dirname( WPCALIBRATE_DDT_BASENAME ) . '/languages'
	);

	// Bootstrap the main plugin instance.
	Plugin::get_instance()->run();
}
add_action( 'plugins_loaded', 'wpcalibrate_ddt_init' );
