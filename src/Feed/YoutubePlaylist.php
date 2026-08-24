<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

defined( 'ABSPATH' ) || exit;

/**
 * Obtiene los vídeos de una playlist pública de YouTube mediante RSS.
 */
final class YoutubePlaylist {

	private const CACHE_TTL = 1800;

	private string $value;

	public function __construct( string $value ) {

		$this->value = trim( $value );

	}

	/**
	 * @return array<int, \DMF\Media\Models\Video>|null Null si la playlist no se pudo cargar.
	 */
	public function getVideos( int $limit = 6 ): ?array {

		$playlistId = $this->normalizePlaylistId();

		if ( null === $playlistId ) {
			return null;
		}

		$xml = $this->getFeedXml( $playlistId );

		if ( null === $xml ) {
			return null;
		}

		$videos = ( new YoutubeRssParser() )->parse( $xml );

		return array_slice( $videos, 0, $limit );

	}

	private function normalizePlaylistId(): ?string {

		if ( $this->isValidPlaylistId( $this->value ) ) {
			return $this->value;
		}

		$url = wp_parse_url( $this->value );

		if ( ! is_array( $url ) || empty( $url['scheme'] ) || empty( $url['host'] ) ) {
			return null;
		}

		if ( ! in_array( strtolower( $url['scheme'] ), [ 'http', 'https' ], true ) ) {
			return null;
		}

		$host = strtolower( $url['host'] );
		$host = preg_replace( '/^www\./', '', $host );

		if ( 'youtube.com' !== $host || 'playlist' !== trim( $url['path'] ?? '', '/' ) ) {
			return null;
		}

		parse_str( $url['query'] ?? '', $query );

		return $this->getValidPlaylistId( $query['list'] ?? '' );

	}

	private function getFeedXml( string $playlistId ): ?string {

		$cacheKey = $this->getCacheKey( $playlistId );
		$xml = get_transient( $cacheKey );

		if ( is_string( $xml ) && '' !== $xml ) {
			return $xml;
		}

		$response = wp_remote_get(
			$this->getFeedUrl( $playlistId ),
			[
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( ! is_string( $body ) || '' === trim( $body ) ) {
			return null;
		}

		set_transient( $cacheKey, $body, self::CACHE_TTL );

		return $body;

	}

	private function getFeedUrl( string $playlistId ): string {

		return sprintf(
			'https://www.youtube.com/feeds/videos.xml?playlist_id=%s',
			rawurlencode( $playlistId )
		);

	}

	private function getCacheKey( string $playlistId ): string {

		return 'dmf_media_playlist_v1_' . md5( $playlistId );

	}

	private function getValidPlaylistId( mixed $value ): ?string {

		if ( ! is_string( $value ) || ! $this->isValidPlaylistId( $value ) ) {
			return null;
		}

		return $value;

	}

	private function isValidPlaylistId( string $playlistId ): bool {

		return 1 === preg_match( '/^PL[A-Za-z0-9_-]+$/', $playlistId );

	}

}
