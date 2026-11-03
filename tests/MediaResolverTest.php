<?php
declare(strict_types=1);

use DMF\Media\Resolver\MediaResolver;

dmf_test(
	'MediaResolver: canal configurado',
	static function (): void {
		dmf_set_channel_options();
		dmf_set_feed();

		$result = ( new MediaResolver() )->resolve( [] );

		assert_false( $result->hasError() );
		assert_count( 3, $result->videos );
	}
);

dmf_test(
	'MediaResolver: limit y offset',
	static function (): void {
		dmf_set_channel_options();
		dmf_set_feed();

		$resolver = new MediaResolver();

		assert_count( 2, $resolver->resolve( [ 'limit' => '2' ] )->videos );
		assert_same( 'Segundo vídeo', $resolver->resolve( [ 'offset' => '1', 'limit' => '1' ] )->videos[0]->title );
		assert_count( 1, $resolver->resolve( [ 'limit' => '0' ] )->videos );
	}
);

dmf_test(
	'MediaResolver: offset como entero (bloque)',
	static function (): void {
		dmf_set_channel_options();
		dmf_set_feed();

		$result = ( new MediaResolver() )->resolve( [ 'offset' => 1, 'limit' => 1 ] );

		assert_count( 1, $result->videos );
		assert_same( 'Segundo vídeo', $result->videos[0]->title );
	}
);

dmf_test(
	'MediaResolver: el límite de ajustes se respeta',
	static function (): void {
		dmf_set_channel_options( '2' );
		dmf_set_feed();

		$result = ( new MediaResolver() )->resolve( [] );

		assert_count( 2, $result->videos );
	}
);

dmf_test(
	'MediaResolver: canal inválido no hace peticiones',
	static function (): void {
		dmf_set_channel_options();

		$result = ( new MediaResolver() )->resolve( [ 'channel' => '@nope' ] );

		assert_not_null( $result->error );
		assert_contains( 'Channel ID', (string) $result->error );
		assert_count( 0, $GLOBALS['dmf_http_calls'] );
	}
);

dmf_test(
	'MediaResolver: playlist por URL de m.youtube.com',
	static function (): void {
		dmf_set_playlist_feed();

		$result = ( new MediaResolver() )->resolve(
			[ 'playlist' => 'https://m.youtube.com/playlist?list=PLabcdefghijklmnop' ]
		);

		assert_count( 3, $result->videos );

		$calls = $GLOBALS['dmf_http_calls'];
		assert_contains( 'playlist_id=PLabcdefghijklmnop', (string) end( $calls ) );
	}
);

dmf_test(
	'MediaResolver: playlist inválida',
	static function (): void {
		$result = ( new MediaResolver() )->resolve( [ 'playlist' => 'PLnope' ] );

		assert_same( 'El identificador de la playlist no es válido.', $result->error );
		assert_count( 0, $GLOBALS['dmf_http_calls'] );
	}
);

dmf_test(
	'MediaResolver: vídeo manual por URL',
	static function (): void {
		dmf_set_oembed();

		$result = ( new MediaResolver() )->resolve(
			[ 'video' => 'https://m.youtube.com/watch?v=AAAAAAAAAAA' ]
		);

		assert_count( 1, $result->videos );
		assert_same( 'Manual', $result->videos[0]->title );
		assert_null( $result->videos[0]->publishedAt );
	}
);

dmf_test(
	'MediaResolver: fallo de oEmbed con caché negativa',
	static function (): void {
		$resolver = new MediaResolver();

		$first  = $resolver->resolve( [ 'video' => 'https://youtu.be/AAAAAAAAAAA' ] );
		$second = $resolver->resolve( [ 'video' => 'https://youtu.be/AAAAAAAAAAA' ] );

		assert_same( 'No se pudo cargar el vídeo solicitado.', $first->error );
		assert_same( 'No se pudo cargar el vídeo solicitado.', $second->error );
		assert_count( 1, $GLOBALS['dmf_http_calls'] );
	}
);

dmf_test(
	'MediaResolver: fallo de feed con caché negativa',
	static function (): void {
		dmf_set_channel_options();

		$resolver = new MediaResolver();

		$first  = $resolver->resolve( [] );
		$second = $resolver->resolve( [] );

		assert_same( 'No se ha podido cargar el contenido.', $first->error );
		assert_same( 'No se ha podido cargar el contenido.', $second->error );
		assert_count( 1, $GLOBALS['dmf_http_calls'] );
	}
);

dmf_test(
	'MediaResolver: opciones corruptas no provocan un fatal',
	static function (): void {
		$GLOBALS['dmf_options']['dmf_media_settings'] = [
			'channel' => [ 'x' ],
			'limit'   => [ 'y' ],
		];

		$result = ( new MediaResolver() )->resolve( [] );

		assert_same( 'Debes configurar un canal de YouTube en Ajustes → DMF Media.', $result->error );
	}
);

dmf_test(
	'MediaResolver: sin canal configurado',
	static function (): void {
		$result = ( new MediaResolver() )->resolve( [] );

		assert_same( 'Debes configurar un canal de YouTube en Ajustes → DMF Media.', $result->error );
	}
);
