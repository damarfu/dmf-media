<?php

declare(strict_types=1);

namespace DMF\Media\Shortcodes;

use DMF\Media\Feed\YoutubeFeed;
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

		$options = get_option( 'dmf_media_settings', [] );

		$channel = ! empty( $atts['channel'] )
			? trim( (string) $atts['channel'] )
			: ( $options['channel'] ?? '' );

		$limit = ! empty( $atts['limit'] )
			? (int) $atts['limit']
			: (int) ( $options['limit'] ?? 6 );

		if ( empty( $channel ) ) {
			return '<p>Debes configurar un canal de YouTube en Ajustes → DMF Media.</p>';
		}

		$feed = new YoutubeFeed( $channel );

		$videos = $feed->getVideos( $limit );

		$renderer = new Renderer();

		return $renderer->render( $videos );

	}

}