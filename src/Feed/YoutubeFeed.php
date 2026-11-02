<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

use DMF\Media\Models\Video;
use DMF\Media\Support\YoutubeId;
use InvalidArgumentException;

defined( 'ABSPATH' ) || exit;

/**
 * Obtiene los vídeos de un canal mediante el RSS público de YouTube.
 */
final class YoutubeFeed {

	private const CACHE_TTL   = 1800;
	private const FAILURE_TTL = 300;

	private string $channelId;

	public function __construct( string $channelId ) {

		$normalized = YoutubeId::channel( $channelId );

		if ( null === $normalized ) {
			throw new InvalidArgumentException( 'Invalid YouTube channel ID.' );
		}

		$this->channelId = $normalized;

	}

	/**
	 * @return array<int, Video>|null Null si el feed no se pudo cargar.
	 */
	public function getVideos( int $limit = 6, int $offset = 0 ): ?array {

		$xml = $this->getFeedXml();

		if ( null === $xml ) {
			return null;
		}

		$videos = ( new YoutubeRssParser() )->parse( $xml );

		return array_slice( $videos, $offset, $limit );

	}

	private function getFeedUrl(): string {

		return sprintf(
			'https://www.youtube.com/feeds/videos.xml?channel_id=%s',
			rawurlencode( $this->channelId )
		);

	}

	private function getCacheKey(): string {

		return 'dmf_media_feed_v2_' . md5( $this->channelId );

	}

	private function getFailureKey(): string {

		return 'dmf_media_feed_fail_v1_' . md5( $this->channelId );

	}

	private function getFeedXml(): ?string {

		$cacheKey = $this->getCacheKey();
		$cached   = get_transient( $cacheKey );

		if ( is_string( $cached ) && '' !== $cached ) {
			return $cached;
		}

		if ( '1' === get_transient( $this->getFailureKey() ) ) {
			return null;
		}

		$body = $this->fetchFeed();

		if ( null === $body ) {
			set_transient( $this->getFailureKey(), '1', self::FAILURE_TTL );

			return null;
		}

		set_transient( $cacheKey, $body, self::CACHE_TTL );

		return $body;

	}

	private function fetchFeed(): ?string {

		$response = wp_remote_get(
			$this->getFeedUrl(),
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

}
