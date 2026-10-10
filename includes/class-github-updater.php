<?php
/**
 * GitHub in-dashboard update checker and provider.
 *
 * @package WPCalibrate\DynamicDateTime
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class GitHubUpdater
 *
 * Checks GitHub repository releases for plugin updates and injects update metadata
 * into the WordPress update pipeline for 1-click dashboard updating.
 */
class GitHubUpdater {

	/**
	 * GitHub repository owner/name.
	 */
	public const REPO = 'zeeshanraza-official/wpcalibrate-dynamic-date-time';

	/**
	 * Transient key for caching GitHub release data.
	 */
	public const CACHE_KEY = 'wpcal_ddt_github_release';

	/**
	 * Cache expiration in seconds (6 hours).
	 */
	public const CACHE_TTL = 21600;

	/**
	 * Initialize WordPress update hooks.
	 */
	public function init(): void {
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_for_updates' ] );
		add_filter( 'plugins_api', [ $this, 'plugin_popup_information' ], 20, 3 );
		add_action( 'upgrader_process_complete', [ $this, 'clear_release_cache' ], 10, 2 );
	}

	/**
	 * Fetch latest release from GitHub API with transient caching.
	 *
	 * @return array<string, mixed>|null Release data or null on failure.
	 */
	public function get_latest_release(): ?array {
		$cached = get_transient( self::CACHE_KEY );
		if ( is_array( $cached ) ) {
			return $cached;
		}

		$url      = sprintf( 'https://api.github.com/repos/%s/releases/latest', self::REPO );
		$response = wp_remote_get(
			$url,
			[
				'timeout'    => 10,
				'user-agent' => 'WPCalibrate-WordPress-Updater/' . WPCALIBRATE_DDT_VERSION,
				'headers'    => [
					'Accept' => 'application/vnd.github.v3+json',
				],
			]
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			// Cache false for 1 hour to prevent flooding API on error.
			set_transient( self::CACHE_KEY, [], 3600 );
			return null;
		}

		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $body ) || empty( $body['tag_name'] ) ) {
			return null;
		}

		set_transient( self::CACHE_KEY, $body, self::CACHE_TTL );
		return $body;
	}

	/**
	 * Inject update package into WordPress plugins transient if a newer version exists.
	 *
	 * @param object|null $transient Plugins update transient.
	 * @return object|null Modified transient.
	 */
	public function check_for_updates( ?object $transient ): ?object {
		if ( ! is_object( $transient ) || empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->get_latest_release();
		if ( empty( $release ) ) {
			return $transient;
		}

		$latest_version = ltrim( (string) $release['tag_name'], 'v' );
		if ( version_compare( $latest_version, WPCALIBRATE_DDT_VERSION, '>' ) ) {
			$download_url = $this->get_package_download_url( $release );

			$item              = new stdClass();
			$item->slug        = 'wpcalibrate-dynamic-date-time';
			$item->plugin      = WPCALIBRATE_DDT_BASENAME;
			$item->new_version = $latest_version;
			$item->url         = $release['html_url'] ?? sprintf( 'https://github.com/%s', self::REPO );
			$item->package     = $download_url;
			$item->icons       = [
				'default' => WPCALIBRATE_DDT_URL . 'branding/icon-dark.png',
			];
			$item->tested      = '7.1.3';
			$item->requires_php = '8.2';

			$transient->response[ WPCALIBRATE_DDT_BASENAME ] = $item;
		}

		return $transient;
	}

	/**
	 * Provide plugin details modal data for WordPress "View version details".
	 *
	 * @param mixed  $result The result object or false.
	 * @param string $action The type of information being requested.
	 * @param object $args   Plugin API arguments.
	 * @return mixed Plugin information or original result.
	 */
	public function plugin_popup_information( mixed $result, string $action, object $args ): mixed {
		if ( 'plugin_information' !== $action || empty( $args->slug ) || 'wpcalibrate-dynamic-date-time' !== $args->slug ) {
			return $result;
		}

		$release = $this->get_latest_release();
		if ( empty( $release ) ) {
			return $result;
		}

		$latest_version = ltrim( (string) $release['tag_name'], 'v' );

		$info                = new stdClass();
		$info->name          = 'WPCalibrate Dynamic Date & Time';
		$info->slug          = 'wpcalibrate-dynamic-date-time';
		$info->version       = $latest_version;
		$info->author        = '<a href="https://wpcalibrate.com">WPCalibrate</a>';
		$info->homepage      = 'https://wpcalibrate.com';
		$info->requires      = '6.0';
		$info->tested        = '7.1.3';
		$info->requires_php  = '8.2';
		$info->download_link = $this->get_package_download_url( $release );
		$info->last_updated  = $release['published_at'] ?? '';
		$info->sections      = [
			'description' => esc_html__( 'Display dynamic dates and times across landing pages, Elementor, Gutenberg, and shortcode builders with site/visitor timezones, offsets, and live updating.', 'wpcalibrate-dynamic-date-time' ),
			'changelog'   => ! empty( $release['body'] ) ? nl2br( esc_html( (string) $release['body'] ) ) : esc_html__( 'Bug fixes and performance improvements.', 'wpcalibrate-dynamic-date-time' ),
		];

		return $info;
	}

	/**
	 * Extract the best ZIP download URL from release assets or zipball.
	 *
	 * @param array<string, mixed> $release Release array.
	 * @return string ZIP URL.
	 */
	private function get_package_download_url( array $release ): string {
		if ( ! empty( $release['assets'] ) && is_array( $release['assets'] ) ) {
			foreach ( $release['assets'] as $asset ) {
				if ( isset( $asset['name'], $asset['browser_download_url'] ) && str_ends_with( (string) $asset['name'], '.zip' ) ) {
					return (string) $asset['browser_download_url'];
				}
			}
		}

		return (string) ( $release['zipball_url'] ?? sprintf( 'https://github.com/%s/archive/refs/tags/%s.zip', self::REPO, $release['tag_name'] ) );
	}

	/**
	 * Clear cached release data upon plugin upgrade.
	 *
	 * @param mixed $upgrader Upgrader instance.
	 * @param array $options  Options array.
	 */
	public function clear_release_cache( mixed $upgrader = null, array $options = [] ): void {
		delete_transient( self::CACHE_KEY );
	}
}
