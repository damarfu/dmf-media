<?php

declare(strict_types=1);

namespace DMF\Media\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Normaliza e identifica los distintos tipos de ID de YouTube.
 *
 * Acepta el identificador desnudo y las URLs públicas más habituales, y
 * devuelve null cuando el valor no se puede interpretar.
 */
final class YoutubeId {

	private const VIDEO_PATTERN    = '/^[A-Za-z0-9_-]{11}$/';
	private const CHANNEL_PATTERN  = '/^UC[A-Za-z0-9_-]{22}$/';
	private const PLAYLIST_PATTERN = '/^[A-Za-z0-9_-]{13,64}$/';

	/**
	 * Devuelve el Channel ID a partir de un ID o de una URL /channel/UC...
	 */
	public static function channel( string $value ): ?string {

		$value = trim( $value );

		if ( self::isChannelId( $value ) ) {
			return $value;
		}

		$url = self::parseUrl( $value );

		if ( null === $url || 'youtube.com' !== self::normalizeHost( (string) $url['host'] ) ) {
			return null;
		}

		$path = (string) ( $url['path'] ?? '' );

		if ( 'channel' !== self::pathSegment( $path, 0 ) ) {
			return null;
		}

		$channelId = self::pathSegment( $path, 1 );

		return self::isChannelId( $channelId ) ? $channelId : null;

	}

	/**
	 * Devuelve el Playlist ID a partir de un ID o de una URL de playlist.
	 *
	 * Se aceptan los formatos /playlist?list=, /watch?v=...&list= y
	 * /embed/videoseries?list=, además de los prefijos PL, UU, LL, FL, RD y
	 * OLAK5uy_. El límite de longitud evita peticiones con valores basura.
	 */
	public static function playlist( string $value ): ?string {

		$value = trim( $value );

		if ( self::isPlaylistId( $value ) ) {
			return $value;
		}

		$url = self::parseUrl( $value );

		if ( null === $url || 'youtube.com' !== self::normalizeHost( (string) $url['host'] ) ) {
			return null;
		}

		$segment = self::pathSegment( (string) ( $url['path'] ?? '' ), 0 );

		if ( ! in_array( $segment, [ 'playlist', 'watch', 'embed' ], true ) ) {
			return null;
		}

		$playlistId = self::queryArgument( $url, 'list' );

		return self::isPlaylistId( $playlistId ) ? $playlistId : null;

	}

	/**
	 * Devuelve el ID de un vídeo a partir de un ID o de una URL de YouTube.
	 *
	 * Se aceptan watch, youtu.be, shorts, embed y live.
	 */
	public static function video( string $value ): ?string {

		$value = trim( $value );

		if ( self::isVideoId( $value ) ) {
			return $value;
		}

		$url = self::parseUrl( $value );

		if ( null === $url ) {
			return null;
		}

		$host = self::normalizeHost( (string) $url['host'] );
		$path = (string) ( $url['path'] ?? '' );

		if ( 'youtu.be' === $host ) {
			$videoId = self::pathSegment( $path, 0 );

			return self::isVideoId( $videoId ) ? $videoId : null;
		}

		if ( 'youtube.com' !== $host ) {
			return null;
		}

		$segment = self::pathSegment( $path, 0 );

		if ( 'watch' === $segment ) {
			$videoId = self::queryArgument( $url, 'v' );

			return self::isVideoId( $videoId ) ? $videoId : null;
		}

		if ( in_array( $segment, [ 'shorts', 'embed', 'live' ], true ) ) {
			$videoId = self::pathSegment( $path, 1 );

			return self::isVideoId( $videoId ) ? $videoId : null;
		}

		return null;

	}

	private static function isVideoId( string $value ): bool {

		return 1 === preg_match( self::VIDEO_PATTERN, $value );

	}

	private static function isChannelId( string $value ): bool {

		return 1 === preg_match( self::CHANNEL_PATTERN, $value );

	}

	private static function isPlaylistId( string $value ): bool {

		if ( self::isChannelId( $value ) ) {
			return false;
		}

		return 1 === preg_match( self::PLAYLIST_PATTERN, $value );

	}

	/**
	 * @return array{scheme?: string, host?: string, path?: string, query?: string}|null
	 */
	private static function parseUrl( string $value ): ?array {

		if ( '' === $value ) {
			return null;
		}

		if ( ! str_contains( $value, '://' ) ) {
			$value = 'https://' . ltrim( $value, '/' );
		}

		$url = wp_parse_url( $value );

		if ( ! is_array( $url ) || empty( $url['host'] ) ) {
			return null;
		}

		$scheme = strtolower( (string) ( $url['scheme'] ?? '' ) );

		if ( ! in_array( $scheme, [ 'http', 'https' ], true ) ) {
			return null;
		}

		return $url;

	}

	private static function normalizeHost( string $host ): string {

		$host = strtolower( trim( $host ) );

		foreach ( [ 'www.', 'm.', 'music.' ] as $prefix ) {
			if ( str_starts_with( $host, $prefix ) ) {
				return substr( $host, strlen( $prefix ) );
			}
		}

		return $host;

	}

	private static function pathSegment( string $path, int $index ): string {

		$segments = explode( '/', trim( $path, '/' ) );

		return (string) ( $segments[ $index ] ?? '' );

	}

	/**
	 * @param array{query?: string} $url
	 */
	private static function queryArgument( array $url, string $name ): string {

		parse_str( (string) ( $url['query'] ?? '' ), $query );

		$value = $query[ $name ] ?? '';

		return is_string( $value ) ? $value : '';

	}

}
