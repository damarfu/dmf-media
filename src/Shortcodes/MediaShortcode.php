<?php

declare(strict_types=1);

namespace DMF\Media\Shortcodes;

use DMF\Media\Feed\YoutubeFeed;
use DMF\Media\Feed\YoutubePlaylist;
use DMF\Media\Feed\YoutubeVideo;
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
				'video'   => '',
				'playlist' => '',
			],
			$atts,
			'dmf_media'
		);

		$renderer = new Renderer();
		$manualVideo = sanitize_text_field( (string) $atts['video'] );

		if ( '' !== $manualVideo ) {
			$video = ( new YoutubeVideo( $manualVideo ) )->getVideo();

			if ( null === $video ) {
				return '<p>No se pudo cargar el vídeo solicitado.</p>';
			}

			return $renderer->render( [ $video ] );
		}

		$options = get_option( 'dmf_media_settings', [] );

		$limit = ! empty( $atts['limit'] )
			? (int) $atts['limit']
			: (int) ( $options['limit'] ?? 6 );

		$limit = max( 1, min( 20, $limit ) );

		$playlist = sanitize_text_field( (string) $atts['playlist'] );

		if ( '' !== $playlist ) {
			$videos = ( new YoutubePlaylist( $playlist ) )->getVideos( $limit );

			if ( null === $videos ) {
				return '<p>No se ha podido cargar el contenido.</p>';
			}

			if ( empty( $videos ) ) {
				return '<p>No hay vídeos disponibles.</p>';
			}

			return $renderer->render( $videos );
		}

		$channel = ! empty( $atts['channel'] )
			? trim( (string) $atts['channel'] )
			: ( $options['channel'] ?? '' );

		if ( empty( $channel ) ) {
			return '<p>Debes configurar un canal de YouTube en Ajustes → DMF Media.</p>';
		}

		$feed = new YoutubeFeed( $channel );

		$videos = $feed->getVideos( $limit );

		return $renderer->render( $videos );

	}

}
