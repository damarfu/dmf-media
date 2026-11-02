<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

use DMF\Media\Models\Video;
use DMF\Media\Support\YoutubeId;

defined( 'ABSPATH' ) || exit;

/**
 * Obtiene los metadatos de un vídeo individual de YouTube mediante oEmbed.
 */
final class YoutubeVideo {

	private const CACHE_TTL   = 1800;
	private const FAILURE_TTL = 300;

	private string $value;

	public function __construct( string $value ) {

		$this->value = trim( $value );

	}

	public function getVideo(): ?Video {

		$videoId = YoutubeId::video( $this->value );

		if ( null === $videoId ) {
			return null;
		}

		$metadata = $this->getMetadata( $videoId );

		if ( null === $metadata ) {
			return null;
		}

		return new Video(
			title: $metadata['title'],
			videoId: $videoId,
			url: $this->getCanonicalUrl( $videoId ),
			thumbnail: $metadata['thumbnail'],
			publishedAt: null
		);

	}

	private function getMetadata( string $videoId ): ?array {

		$cacheKey = $this->getCacheKey( $videoId );
		$metadata = get_transient( $cacheKey );

		if ( $this->isValidMetadata( $metadata ) ) {
			return $metadata;
		}

		if ( '1' === get_transient( $this->getFailureKey( $videoId ) ) ) {
			return null;
		}

		$metadata = $this->fetchMetadata( $videoId );

		if ( null === $metadata ) {
			set_transient( $this->getFailureKey( $videoId ), '1', self::FAILURE_TTL );

			return null;
		}

		set_transient( $cacheKey, $metadata, self::CACHE_TTL );

		return $metadata;

	}

	private function fetchMetadata( string $videoId ): ?array {

		$response = wp_remote_get(
			add_query_arg(
				[
					'url'    => $this->getCanonicalUrl( $videoId ),
					'format' => 'json',
				],
				'https://www.youtube.com/oembed'
			),
			[
				'timeout' => 10,
			]
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}

		$contentType = wp_remote_retrieve_header( $response, 'content-type' );

		if ( is_string( $contentType ) && '' !== $contentType && ! str_contains( strtolower( $contentType ), 'json' ) ) {
			return null;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( (string) $body, true );

		if ( ! is_array( $data ) || ! is_string( $data['title'] ?? null ) || '' === trim( $data['title'] ) ) {
			return null;
		}

		$thumbnail = $data['thumbnail_url'] ?? '';

		if ( ! is_string( $thumbnail ) || false === filter_var( $thumbnail, FILTER_VALIDATE_URL ) ) {
			$thumbnail = $this->getDefaultThumbnailUrl( $videoId );
		}

		return [
			'title'     => trim( $data['title'] ),
			'thumbnail' => $thumbnail,
		];

	}

	private function getCacheKey( string $videoId ): string {

		return 'dmf_media_video_v1_' . md5( $videoId );

	}

	private function getFailureKey( string $videoId ): string {

		return 'dmf_media_video_fail_v1_' . md5( $videoId );

	}

	private function getCanonicalUrl( string $videoId ): string {

		return 'https://www.youtube.com/watch?v=' . rawurlencode( $videoId );

	}

	private function getDefaultThumbnailUrl( string $videoId ): string {

		return 'https://i.ytimg.com/vi/' . rawurlencode( $videoId ) . '/hqdefault.jpg';

	}

	private function isValidMetadata( mixed $metadata ): bool {

		return is_array( $metadata )
			&& is_string( $metadata['title'] ?? null )
			&& '' !== trim( $metadata['title'] )
			&& is_string( $metadata['thumbnail'] ?? null )
			&& false !== filter_var( $metadata['thumbnail'], FILTER_VALIDATE_URL );

	}

}
