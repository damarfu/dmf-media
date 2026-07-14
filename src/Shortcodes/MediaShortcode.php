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

	public function render( array $atts = [] ): string {

		$atts = shortcode_atts(
			[
				'channel' => '',
				'limit'   => '',
			],
			$atts,
			'dmf_media'
		);

		$renderer = new Renderer();

		return $renderer->render( $atts );

	}

}