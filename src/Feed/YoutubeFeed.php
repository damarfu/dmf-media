<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

use DateTimeImmutable;
use DMF\Media\Models\Video;

defined( 'ABSPATH' ) || exit;

final class YoutubeFeed {

	private string $channelId;

	public function __construct( string $channelId ) {

		$this->channelId = trim( $channelId );

	}

	private function getFeedUrl(): string {

		return sprintf(
			'https://www.youtube.com/feeds/videos.xml?channel_id=%s',
			rawurlencode( $this->channelId )
		);

	}

	/**
	 * Obtiene los vídeos del canal.
	 *
	 * @return Video[]
	 */
	public function getVideos(): array {

		$response = wp_remote_get(
			$this->getFeedUrl(),
			[
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return [];
		}

		libxml_use_internal_errors( true );

		$xml = simplexml_load_string( $body );

		if ( ! $xml ) {
			return [];
		}

		$media = $xml->getNamespaces( true );

		$videos = [];

		foreach ( $xml->entry as $entry ) {

			$mediaGroup = $entry->children( $media['media'] )->group;

			$videos[] = new Video(
				title: (string) $entry->title,
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