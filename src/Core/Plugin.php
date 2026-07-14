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

	}

}