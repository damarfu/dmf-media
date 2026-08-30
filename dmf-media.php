<?php
/**
 * Plugin Name: DMF Media
 * Plugin URI: https://github.com/damarfu/dmf-media
 * Description: Display media feeds from YouTube and other platforms.
 * Version: 0.2.3
 * Requires at least: 6.7
 * Requires PHP: 8.1
 * Author: David Martínez
 * License: GPL-2.0-or-later
 * Text Domain: dmf-media
 */

defined( 'ABSPATH' ) || exit;

/*
|--------------------------------------------------------------------------
| Composer Autoload
|--------------------------------------------------------------------------
*/

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	static function ( array $links ): array {

		$settingsLink = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php?page=dmf-media' ) ),
			esc_html__( 'Ajustes', 'dmf-media' )
		);

		array_unshift( $links, $settingsLink );

		return $links;

	}
);

/*
|--------------------------------------------------------------------------
| Boot Plugin
|--------------------------------------------------------------------------
*/

DMF\Media\Core\Plugin::boot();
