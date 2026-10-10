<?php
/**
 * Admin coordinator class.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin
 *
 * Handles admin menu, settings routing, and plugin action links.
 */
class Admin {

	/**
	 * Menu instance.
	 *
	 * @var Menu
	 */
	private Menu $menu;

	/**
	 * Settings instance.
	 *
	 * @var Settings
	 */
	private Settings $settings;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->settings = new Settings();
		$this->menu     = new Menu( $this->settings );
	}

	/**
	 * Initialize admin hooks.
	 */
	public function init(): void {
		$this->menu->init();
		$this->settings->init();

		// Add settings links to plugins list table.
		add_filter( 'plugin_action_links_' . WPCALIBRATE_DDT_BASENAME, [ $this, 'add_action_links' ] );
	}

	/**
	 * Add plugin action links (Settings, Support) to the Plugins screen.
	 *
	 * @param array<string, string> $links Existing action links.
	 * @return array<string, string> Modified action links.
	 */
	public function add_action_links( array $links ): array {
		$settings_url = admin_url( 'admin.php?page=wpcalibrate-dynamic-date-time' );
		$support_url  = admin_url( 'admin.php?page=wpcalibrate-dynamic-date-time&tab=support' );

		$custom_links = [
			'settings' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( $settings_url ),
				esc_html__( 'Settings', 'wpcalibrate-dynamic-date-time' )
			),
			'support'  => sprintf(
				'<a href="%s">%s</a>',
				esc_url( $support_url ),
				esc_html__( 'Support', 'wpcalibrate-dynamic-date-time' )
			),
		];

		return array_merge( $custom_links, $links );
	}
}
