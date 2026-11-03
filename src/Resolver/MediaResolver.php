<?php

declare(strict_types=1);

namespace DMF\Media\Resolver;

use DMF\Media\Feed\YoutubeFeed;
use DMF\Media\Feed\YoutubePlaylist;
use DMF\Media\Feed\YoutubeVideo;
use DMF\Media\Support\YoutubeId;

defined( 'ABSPATH' ) || exit;

/**
 * Decide qué fuente se muestra y resuelve los vídeos.
 *
 * Es el punto único de entrada para el shortcode y, más adelante, para el
 * bloque de Gutenberg. Prioridad: video > playlist > channel.
 */
final class MediaResolver {

	public const DEFAULT_LIMIT = 6;
	public const MAX_LIMIT     = 15;

	/**
	 * @param array<string, mixed> $args
	 */
	public function resolve( array $args ): MediaResult {

		$options = $this->getOptions();
		$video   = $this->argument( $args, 'video' );

		if ( '' !== $video ) {
			return $this->resolveVideo( $video );
		}

		$limit  = $this->resolveLimit( $args, $options );
		$offset = $this->resolveOffset( $args );

		$playlist = $this->argument( $args, 'playlist' );

		if ( '' !== $playlist ) {
			return $this->resolvePlaylist( $playlist, $limit, $offset );
		}

		$channel = $this->argument( $args, 'channel' );

		if ( '' === $channel ) {
			$channel = isset( $options['channel'] ) && is_string( $options['channel'] )
				? trim( $options['channel'] )
				: '';
		}

		if ( '' === $channel ) {
			return MediaResult::failure( 'Debes configurar un canal de YouTube en Ajustes → DMF Media.' );
		}

		return $this->resolveChannel( $channel, $limit, $offset );

	}

	private function resolveVideo( string $value ): MediaResult {

		$videoId = YoutubeId::video( $value );

		if ( null === $videoId ) {
			return MediaResult::failure( 'El vídeo indicado no es válido. Usa un ID de YouTube o una URL de youtube.com, youtu.be o shorts.' );
		}

		$video = ( new YoutubeVideo( $videoId ) )->getVideo();

		if ( null === $video ) {
			return MediaResult::failure( 'No se pudo cargar el vídeo solicitado.' );
		}

		return MediaResult::success( [ $video ] );

	}

	private function resolvePlaylist( string $value, int $limit, int $offset ): MediaResult {

		$playlistId = YoutubeId::playlist( $value );

		if ( null === $playlistId ) {
			return MediaResult::failure( 'El identificador de la playlist no es válido.' );
		}

		$videos = ( new YoutubePlaylist( $playlistId ) )->getVideos( $limit, $offset );

		if ( null === $videos ) {
			return MediaResult::failure( 'No se ha podido cargar el contenido.' );
		}

		return MediaResult::success( $videos );

	}

	private function resolveChannel( string $value, int $limit, int $offset ): MediaResult {

		$channelId = YoutubeId::channel( $value );

		if ( null === $channelId ) {
			return MediaResult::failure( 'El Channel ID no es válido: debe empezar por «UC» y tener 24 caracteres. Los canales con «@usuario» todavía no están admitidos.' );
		}

		$videos = ( new YoutubeFeed( $channelId ) )->getVideos( $limit, $offset );

		if ( null === $videos ) {
			return MediaResult::failure( 'No se ha podido cargar el contenido.' );
		}

		return MediaResult::success( $videos );

	}

	/**
	 * @param array<string, mixed> $args
	 * @param array<string, mixed> $options
	 */
	private function resolveLimit( array $args, array $options ): int {

		$raw = $args['limit'] ?? null;

		if ( is_int( $raw ) || ( is_string( $raw ) && '' !== trim( $raw ) && is_numeric( trim( $raw ) ) ) ) {
			return max( 1, min( self::MAX_LIMIT, (int) $raw ) );
		}

		$stored = $options['limit'] ?? null;
		$limit  = is_numeric( $stored ) ? (int) $stored : self::DEFAULT_LIMIT;

		return max( 1, min( self::MAX_LIMIT, $limit ) );

	}

	/**
	 * @param array<string, mixed> $args
	 */
	private function resolveOffset( array $args ): int {

		$raw = $args['offset'] ?? 0;

		return is_numeric( $raw ) ? max( 0, (int) $raw ) : 0;

	}

	/**
	 * @param array<string, mixed> $args
	 */
	private function argument( array $args, string $key ): string {

		$value = $args[ $key ] ?? '';

		return is_string( $value ) ? trim( $value ) : '';

	}

	/**
	 * @return array<string, mixed>
	 */
	private function getOptions(): array {

		$options = get_option( 'dmf_media_settings', [] );

		return is_array( $options ) ? $options : [];

	}

}
