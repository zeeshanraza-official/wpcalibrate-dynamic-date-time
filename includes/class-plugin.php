<?php
/**
 * Main plugin orchestrator.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 *
 * Coordinates initialization of admin, public, shortcodes, and assets.
 */
class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Admin controller instance.
	 *
	 * @var Admin|null
	 */
	private ?Admin $admin = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return self
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Private to prevent direct instantiation.
	}

	/**
	 * Run the plugin components.
	 */
	public function run(): void {
		// Initialize shortcodes.
		Shortcodes::register();

		// Initialize asset manager.
		Assets::init();

		// Initialize admin area if in admin context.
		if ( is_admin() ) {
			$this->admin = new Admin();
			$this->admin->init();

			$updater = new GitHubUpdater();
			$updater->init();
		}
	}

	/**
	 * Get the admin instance.
	 *
	 * @return Admin|null
	 */
	public function get_admin(): ?Admin {
		return $this->admin;
	}
}
