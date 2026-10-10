<?php
/**
 * Settings and presets controller.
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
 * Class Settings
 *
 * Manages admin settings tabs, form saving, preset CRUD, and security validation.
 */
class Settings {

	/**
	 * Notice message to display.
	 *
	 * @var array<string, string>|null
	 */
	private ?array $notice = null;

	/**
	 * Initialize form handlers.
	 */
	public function init(): void {
		add_action( 'admin_init', [ $this, 'handle_form_submissions' ] );
	}

	/**
	 * Handle settings and preset form submissions.
	 */
	public function handle_form_submissions(): void {
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Handle general settings save.
		if ( isset( $_POST['wpcal_ddt_action'] ) && 'save_settings' === $_POST['wpcal_ddt_action'] ) {
			check_admin_referer( 'wpcal_ddt_save_settings', 'wpcal_ddt_nonce' );
			$this->save_general_settings();
			return;
		}

		// Handle preset save (add or edit).
		if ( isset( $_POST['wpcal_ddt_action'] ) && 'save_preset' === $_POST['wpcal_ddt_action'] ) {
			check_admin_referer( 'wpcal_ddt_save_preset', 'wpcal_ddt_nonce' );
			$this->save_preset();
			return;
		}

		// Handle preset delete.
		if ( isset( $_GET['action'] ) && 'delete_preset' === $_GET['action'] && isset( $_GET['preset_slug'] ) ) {
			$slug = sanitize_key( (string) $_GET['preset_slug'] );
			check_admin_referer( 'wpcal_ddt_delete_' . $slug, '_wpnonce' );
			$this->delete_preset( $slug );
		}
	}

	/**
	 * Save general plugin settings.
	 */
	private function save_general_settings(): void {
		$raw = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? $_POST['settings'] : [];

		$clean = [
			'default_date_format'      => FormatValidator::validate( (string) ( $raw['default_date_format'] ?? 'F j, Y' ), 'date' ),
			'default_time_format'      => FormatValidator::validate( (string) ( $raw['default_time_format'] ?? 'g:i A' ), 'time' ),
			'default_datetime_format'  => FormatValidator::validate( (string) ( $raw['default_datetime_format'] ?? 'F j, Y g:i A' ), 'datetime' ),
			'default_timezone_mode'    => in_array( $raw['default_timezone_mode'] ?? 'site', [ 'site', 'visitor' ], true ) ? $raw['default_timezone_mode'] : 'site',
			'enable_visitor_timezone'  => ! empty( $raw['enable_visitor_timezone'] ) ? 1 : 0,
			'enable_live_time'         => ! empty( $raw['enable_live_time'] ) ? 1 : 0,
			'delete_data_on_uninstall' => ! empty( $raw['delete_data_on_uninstall'] ) ? 1 : 0,
		];

		update_option( 'wpcalibrate_ddt_settings', $clean );

		$redirect_url = add_query_arg(
			[
				'page'    => Menu::PAGE_SLUG,
				'tab'     => 'general',
				'message' => 'settings_saved',
			],
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Save a preset.
	 */
	private function save_preset(): void {
		$raw           = isset( $_POST['preset'] ) && is_array( $_POST['preset'] ) ? $_POST['preset'] : [];
		$existing_slug = ! empty( $_POST['existing_slug'] ) ? sanitize_key( (string) $_POST['existing_slug'] ) : null;

		$result = PresetRepository::save( $raw, $existing_slug );

		if ( is_wp_error( $result ) ) {
			$redirect_url = add_query_arg(
				[
					'page'  => Menu::PAGE_SLUG,
					'tab'   => 'presets',
					'error' => rawurlencode( $result->get_error_message() ),
				],
				admin_url( 'admin.php' )
			);
			wp_safe_redirect( $redirect_url );
			exit;
		}

		$redirect_url = add_query_arg(
			[
				'page'    => Menu::PAGE_SLUG,
				'tab'     => 'presets',
				'message' => 'preset_saved',
			],
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Delete a preset.
	 *
	 * @param string $slug Slug to delete.
	 */
	private function delete_preset( string $slug ): void {
		PresetRepository::delete( $slug );

		$redirect_url = add_query_arg(
			[
				'page'    => Menu::PAGE_SLUG,
				'tab'     => 'presets',
				'message' => 'preset_deleted',
			],
			admin_url( 'admin.php' )
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Render the main plugin settings page.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'wpcalibrate-dynamic-date-time' ) );
		}

		$current_page = isset( $_GET['page'] ) ? sanitize_key( (string) $_GET['page'] ) : '';
		$default_tab  = ( Menu::PAGE_SLUG === $current_page ) ? 'general' : 'overview';
		$current_tab  = isset( $_GET['tab'] ) ? sanitize_key( (string) $_GET['tab'] ) : $default_tab;
		if ( ! in_array( $current_tab, [ 'overview', 'general', 'presets', 'shortcodes', 'support' ], true ) ) {
			$current_tab = $default_tab;
		}

		$settings = get_option( 'wpcalibrate_ddt_settings', [] );
		if ( ! is_array( $settings ) ) {
			$settings = Activator::get_default_settings();
		}

		$presets = PresetRepository::get_all();

		// Check for messages/errors in URL.
		$message_code = isset( $_GET['message'] ) ? sanitize_key( (string) $_GET['message'] ) : null;
		$error_text   = isset( $_GET['error'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['error'] ) ) : null;

		require WPCALIBRATE_DDT_DIR . 'admin/views/settings-page.php';
	}
}
