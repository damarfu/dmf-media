<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

use DMF\Media\Models\Video;
use DMF\Media\Support\YoutubeId;

defined( 'ABSPATH' ) || exit;

/**
 * Obtiene los vídeos de una playlist pública de YouTube mediante RSS.
 */
final class YoutubePlaylist {

	private const CACHE_TTL   = 1800;
	private const FAILURE_TTL = 300;

	private string $value;

	public function __construct( string $value ) {

		$this->value = trim( $value );

	}

	/**
	 * @return array<int, Video>|null Null si la playlist no se pudo cargar.
	 */
	public function getVideos( int $limit = 6, int $offset = 0 ): ?array {

		$playlistId = YoutubeId::playlist( $this->value );

		if ( null === $playlistId ) {
			return null;
		}

		$xml = $this->getFeedXml( $playlistId );

		if ( null === $xml ) {
			return null;
		}

		$videos = ( new YoutubeRssParser() )->parse( $xml );

		return array_slice( $videos, $offset, $limit );

	}

	private function getFeedXml( string $playlistId ): ?string {

		$cacheKey = $this->getCacheKey( $playlistId );
		$cached   = get_transient( $cacheKey );

		if ( is_string( $cached ) && '' !== $cached ) {
			return $cached;
		}

		if ( '1' === get_transient( $this->getFailureKey( $playlistId ) ) ) {
			return null;
		}

		$body = $this->fetchFeed( $playlistId );

		if ( null === $body ) {
			set_transient( $this->getFailureKey( $playlistId ), '1', self::FAILURE_TTL );

			return null;
		}

		set_transient( $cacheKey, $body, self::CACHE_TTL );

		return $body;

	}

	private function fetchFeed( string $playlistId ): ?string {

		$response = wp_remote_get(
			$this->getFeedUrl( $playlistId ),
			[
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		$contentType = wp_remote_retrieve_header( $response, 'content-type' );

		if ( is_string( $contentType ) && '' !== $contentType && ! str_contains( strtolower( $contentType ), 'xml' ) ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( ! is_string( $body ) || '' === trim( $body ) ) {
			return null;
		}

		if ( ! ( new YoutubeRssParser() )->isValid( $body ) ) {
			return null;
		}

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

	private function getFailureKey( string $playlistId ): string {

		return 'dmf_media_playlist_fail_v1_' . md5( $playlistId );

	}

}
