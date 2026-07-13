<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

defined( 'ABSPATH' ) || exit;

final class YoutubeFeed {

	private string $channelId;

	public function __construct( string $channelId ) {

		$this->channelId = trim( $channelId );

	}

	public function getFeedUrl(): string {

		return sprintf(
			'https://www.youtube.com/feeds/videos.xml?channel_id=%s',
			rawurlencode( $this->channelId )
		);

	}

	public function fetch(): \SimpleXMLElement|false {

		$url = $this->getFeedUrl();

		$response = wp_remote_get(
			$url,
			[
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return false;
		}

		libxml_use_internal_errors( true );

		$xml = simplexml_load_string( $body );

		return $xml ?: false;

	}

}