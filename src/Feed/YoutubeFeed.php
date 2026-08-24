<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

defined( 'ABSPATH' ) || exit;

final class YoutubeFeed {

	private const CACHE_TTL = 1800;

	private string $channelId;

	public function __construct( string $channelId ) {

		$this->channelId = trim( $channelId );

	}

	/**
	 * @return Video[]
	 */
	public function getVideos( int $limit = 6 ): array {

		$xml = $this->getFeedXml();

		if ( '' === $xml ) {
			return [];
		}

		$videos = ( new YoutubeRssParser() )->parse( $xml );

		return array_slice( $videos, 0, $limit );

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

	private function getFeedXml(): string {

		$cacheKey = $this->getCacheKey();

		$xml = get_transient( $cacheKey );

		if ( is_string( $xml ) && '' !== $xml ) {
			return $xml;
		}

		$response = wp_remote_get(
			$this->getFeedUrl(),
			[
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return '';
		}

		set_transient(
			$cacheKey,
			$body,
			self::CACHE_TTL
		);

		return $body;

	}

}
