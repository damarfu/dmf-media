<?php
declare(strict_types=1);

use DMF\Media\Feed\YoutubeRssParser;
use DMF\Media\Render\Modal;
use DMF\Media\Render\Renderer;

dmf_test(
	'Renderer: tarjetas, escapado y sin modal incrustado',
	static function (): void {
		$videos = ( new YoutubeRssParser() )->parse( dmf_fixture( 'feed.xml' ) );
		$html   = ( new Renderer() )->render( $videos );

		assert_same( 3, substr_count( $html, 'dmf-video-card' ) );
		assert_contains( 'data-video-id="AAAAAAAAAAA"', $html );
		assert_contains( 'alt=""', $html );
		assert_contains( 'Primero &amp; directo', $html );
		assert_not_contains( 'dmf-video-modal', $html );
		assert_true( Modal::isNeeded() );
	}
);

dmf_test(
	'Renderer: mensaje cuando no hay vídeos',
	static function (): void {
		$html = ( new Renderer() )->render( [] );

		assert_contains( 'dmf-media-message', $html );
		assert_contains( 'No hay vídeos disponibles.', $html );
	}
);
