<?php

declare(strict_types=1);

namespace DMF\Media\Core;

use DMF\Media\Admin\Settings;
use DMF\Media\Shortcodes\MediaShortcode;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	public static function boot(): void {

		$settings = new Settings();

		add_action(
			'init',
			static function () {

				(new MediaShortcode())->register();

			}
		);

		add_action(
			'admin_menu',
			[ $settings, 'addMenu' ]
		);

		add_action(
			'admin_init',
			[ $settings, 'registerSettings' ]
		);

		add_action(
			'wp_enqueue_scripts',
			[ self::class, 'enqueueAssets' ]
		);

	}

	public static function enqueueAssets(): void {

		wp_enqueue_style(
			'dmf-media',
			plugins_url(
				'assets/css/frontend.css',
				dirname( __DIR__, 2 ) . '/dmf-media.php'
			),
			[],
			'0.1.0'
		);

		wp_enqueue_script(
			'dmf-media',
			plugins_url(
				'assets/js/frontend.js',
				dirname( __DIR__, 2 ) . '/dmf-media.php'
			),
			[],
			'0.1.0',
			true
		);

	}

}