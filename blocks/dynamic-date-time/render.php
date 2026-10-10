<?php
/**
 * Server-side render template for Gutenberg Dynamic Date & Time block.
 *
 * @package WPCalibrate\DynamicDateTime
 *
 * @var array<string, mixed> $attributes Block attributes.
 * @var string               $content    Block inner content.
 * @var WP_Block             $block      Block instance.
 */

declare(strict_types=1);

namespace WPCalibrate\DynamicDateTime;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$atts = [
	'type'     => $attributes['type'] ?? 'date',
	'format'   => $attributes['format'] ?? '',
	'timezone' => $attributes['timezone'] ?? 'site',
	'offset'   => $attributes['offset'] ?? '',
	'live'     => ! empty( $attributes['live'] ) ? 'yes' : 'no',
	'preset'   => $attributes['preset'] ?? '',
	'class'    => $attributes['className'] ?? '',
];

echo DateTimeService::render( $atts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
