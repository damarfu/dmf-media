<?php
declare(strict_types=1);

use DMF\Media\Blocks\MediaBlock;

dmf_test(
	'MediaBlock: canal por defecto y wrapper',
	static function (): void {
		dmf_set_channel_options();
		dmf_set_feed();

		$html = MediaBlock::render(
			[
				'source' => 'default',
				'limit'  => 2,
			]
		);

		assert_same( 2, substr_count( $html, 'dmf-video-card' ) );
		assert_contains( 'wp-block-dmf-media', $html );
		assert_not_contains( 'dmf-media-message', $html );
	}
);

dmf_test(
	'MediaBlock: canal explícito vacío muestra un mensaje',
	static function (): void {
		$html = MediaBlock::render( [ 'source' => 'channel' ] );

		assert_contains( 'Escribe el Channel ID o la URL del canal.', $html );
		assert_count( 0, $GLOBALS['dmf_http_calls'] );
	}
);

dmf_test(
	'MediaBlock: vídeo concreto',
	static function (): void {
		dmf_set_oembed();

		$html = MediaBlock::render(
			[
				'source' => 'video',
				'video'  => 'https://youtu.be/AAAAAAAAAAA',
			]
		);

		assert_same( 1, substr_count( $html, 'dmf-video-card' ) );
		assert_contains( 'Manual', $html );
	}
);

dmf_test(
	'MediaBlock: playlist con offset',
	static function (): void {
		dmf_set_playlist_feed();

		$html = MediaBlock::render(
			[
				'source'   => 'playlist',
				'playlist' => 'PLabcdefghijklmnop',
				'offset'   => 1,
				'limit'    => 1,
			]
		);

		assert_same( 1, substr_count( $html, 'dmf-video-card' ) );
		assert_contains( 'Segundo vídeo', $html );
	}
);

dmf_test(
	'MediaBlock: limit 0 usa el ajuste del plugin',
	static function (): void {
		dmf_set_channel_options( '6' );
		dmf_set_feed();

		$html = MediaBlock::render(
			[
				'source' => 'default',
				'limit'  => 0,
			]
		);

		assert_same( 3, substr_count( $html, 'dmf-video-card' ) );
	}
);
