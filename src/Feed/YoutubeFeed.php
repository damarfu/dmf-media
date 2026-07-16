<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

use DateTimeImmutable;
use DMF\Media\Models\Video;

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

		$videos = $this->parseVideos( $xml );

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

	/**
	 * @return Video[]
	 */
	private function parseVideos( string $xmlString ): array {

		libxml_use_internal_errors( true );

		$xml = simplexml_load_string( $xmlString );

		if ( ! $xml ) {
			return [];
		}

		$namespaces = $xml->getNamespaces( true );

		$videos = [];

		foreach ( $xml->entry as $entry ) {

			$mediaGroup = $entry
				->children( $namespaces['media'] )
				->group;

			$youtube = $entry->children(
				$namespaces['yt']
			);

			$videos[] = new Video(
				title: (string) $entry->title,
				videoId: (string) $youtube->videoId,
				url: (string) $entry->link['href'],
				thumbnail: (string) $mediaGroup->thumbnail->attributes()->url,
				publishedAt: new DateTimeImmutable(
					(string) $entry->published
				),
				description: (string) $mediaGroup->description
			);

		}

		return $videos;

	}

}