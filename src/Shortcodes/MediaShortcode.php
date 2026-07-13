<?php

declare(strict_types=1);

namespace DMF\Media\Shortcodes;

use DMF\Media\Render\Renderer;

defined( 'ABSPATH' ) || exit;

final class MediaShortcode {

	public function register(): void {

		add_shortcode(
			'dmf_media',
			[ $this, 'render' ]
		);

	}

	public function render(): string {

		$renderer = new Renderer();

		return $renderer->render();

	}

}