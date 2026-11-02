<?php

declare(strict_types=1);

namespace DMF\Media\Shortcodes;

use DMF\Media\Render\Renderer;
use DMF\Media\Resolver\MediaResolver;

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
				'channel'  => '',
				'limit'    => '',
				'offset'   => '',
				'video'    => '',
				'playlist' => '',
			],
			$atts,
			'dmf_media'
		);

		$result = ( new MediaResolver() )->resolve( $atts );

		if ( $result->hasError() ) {
			return sprintf(
				'<p class="dmf-media-message">%s</p>',
				esc_html( (string) $result->error )
			);
		}

		return ( new Renderer() )->render( $result->videos );

	}

}
