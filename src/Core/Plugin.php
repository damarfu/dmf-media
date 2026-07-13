<?php

declare(strict_types=1);

namespace DMF\Media\Core;

use DMF\Media\Shortcodes\MediaShortcode;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	public static function boot(): void {

		add_action(
			'init',
			static function () {

				$shortcode = new MediaShortcode();
				$shortcode->register();

			}
		);

	}

}