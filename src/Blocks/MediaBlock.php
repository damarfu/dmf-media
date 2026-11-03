<?php

declare(strict_types=1);

namespace DMF\Media\Blocks;

use DMF\Media\Core\Plugin;
use DMF\Media\Render\Renderer;
use DMF\Media\Resolver\MediaResolver;
use DMF\Media\Resolver\MediaResult;

defined( 'ABSPATH' ) || exit;

/**
 * Render del bloque dmf/media.
 *
 * Traduce los atributos del editor a los argumentos que ya entiende
 * MediaResolver y reutiliza el mismo Renderer que el shortcode.
 */
final class MediaBlock {

	/**
	 * @param array<string, mixed> $attributes
	 */
	public static function render( array $attributes ): string {

		Plugin::enqueueAssets();

		$result = self::resolve( $attributes );

		$inner = $result->hasError()
			? sprintf(
				'<p class="dmf-media-message">%s</p>',
				esc_html( (string) $result->error )
			)
			: ( new Renderer() )->render( $result->videos );

		return sprintf(
			'<div %s>%s</div>',
			get_block_wrapper_attributes( [ 'class' => 'dmf-media-block' ] ),
			$inner
		);

	}

	/**
	 * @param array<string, mixed> $attributes
	 */
	private static function resolve( array $attributes ): MediaResult {

		$args = [
			'limit'  => self::limit( $attributes ),
			'offset' => self::number( $attributes, 'offset' ),
		];

		switch ( self::source( $attributes ) ) {
			case 'video':
				$video = self::text( $attributes, 'video' );

				if ( '' === $video ) {
					return MediaResult::failure( 'Escribe el ID o la URL del vídeo.' );
				}

				$args['video'] = $video;
				break;

			case 'playlist':
				$playlist = self::text( $attributes, 'playlist' );

				if ( '' === $playlist ) {
					return MediaResult::failure( 'Escribe el Playlist ID o la URL de la playlist.' );
				}

				$args['playlist'] = $playlist;
				break;

			case 'channel':
				$channel = self::text( $attributes, 'channel' );

				if ( '' === $channel ) {
					return MediaResult::failure( 'Escribe el Channel ID o la URL del canal.' );
				}

				$args['channel'] = $channel;
				break;
		}

		return ( new MediaResolver() )->resolve( $args );

	}

	/**
	 * @param array<string, mixed> $attributes
	 */
	private static function source( array $attributes ): string {

		$source = self::text( $attributes, 'source' );

		return in_array( $source, [ 'default', 'channel', 'playlist', 'video' ], true )
			? $source
			: 'default';

	}

	/**
	 * @param array<string, mixed> $attributes
	 * @return int|string
	 */
	private static function limit( array $attributes ) {

		$limit = self::number( $attributes, 'limit' );

		return $limit > 0 ? $limit : '';

	}

	/**
	 * @param array<string, mixed> $attributes
	 */
	private static function text( array $attributes, string $key ): string {

		$value = $attributes[ $key ] ?? '';

		return is_string( $value ) ? trim( $value ) : '';

	}

	/**
	 * @param array<string, mixed> $attributes
	 */
	private static function number( array $attributes, string $key, int $default = 0 ): int {

		$value = $attributes[ $key ] ?? $default;

		return is_numeric( $value ) ? (int) $value : $default;

	}

}
