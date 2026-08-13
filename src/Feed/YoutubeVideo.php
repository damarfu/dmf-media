<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

use DateTimeImmutable;
use DMF\Media\Models\Video;

defined( 'ABSPATH' ) || exit;

/**
 * Obtiene los metadatos de un vídeo individual de YouTube mediante oEmbed.
 */
final class YoutubeVideo {

	private const CACHE_TTL = 1800;

	private string $value;

	public function __construct( string $value ) {

		$this->value = trim( $value );

	}

	public function getVideo(): ?Video {

		$videoId = $this->normalizeVideoId();

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
			publishedAt: new DateTimeImmutable( '@0' )
		);

	}

	private function normalizeVideoId(): ?string {

		if ( $this->isValidVideoId( $this->value ) ) {
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

		if ( 'youtu.be' === $host ) {
			$pathParts = explode( '/', trim( $url['path'] ?? '', '/' ) );

			return $this->getValidVideoId( $pathParts[0] ?? '' );
		}

		if ( 'youtube.com' !== $host ) {
			return null;
		}

		$path = trim( $url['path'] ?? '', '/' );

		if ( 'watch' === $path ) {
			parse_str( $url['query'] ?? '', $query );

			return $this->getValidVideoId( $query['v'] ?? '' );
		}

		$pathParts = explode( '/', $path );

		if ( 'shorts' === ( $pathParts[0] ?? '' ) ) {
			return $this->getValidVideoId( $pathParts[1] ?? '' );
		}

		return null;

	}

	private function getMetadata( string $videoId ): ?array {

		$cacheKey = $this->getCacheKey( $videoId );
		$metadata = get_transient( $cacheKey );

		if ( $this->isValidMetadata( $metadata ) ) {
			return $metadata;
		}

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

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $data ) || ! is_string( $data['title'] ?? null ) || '' === trim( $data['title'] ) ) {
			return null;
		}

		$thumbnail = $data['thumbnail_url'] ?? '';

		if ( ! is_string( $thumbnail ) || false === filter_var( $thumbnail, FILTER_VALIDATE_URL ) ) {
			$thumbnail = $this->getDefaultThumbnailUrl( $videoId );
		}

		$metadata = [
			'title'     => trim( $data['title'] ),
			'thumbnail' => $thumbnail,
		];

		set_transient( $cacheKey, $metadata, self::CACHE_TTL );

		return $metadata;

	}

	private function getCacheKey( string $videoId ): string {

		return 'dmf_media_video_v1_' . md5( $videoId );

	}

	private function getCanonicalUrl( string $videoId ): string {

		return 'https://www.youtube.com/watch?v=' . rawurlencode( $videoId );

	}

	private function getDefaultThumbnailUrl( string $videoId ): string {

		return 'https://i.ytimg.com/vi/' . rawurlencode( $videoId ) . '/hqdefault.jpg';

	}

	private function getValidVideoId( mixed $value ): ?string {

		if ( ! is_string( $value ) || ! $this->isValidVideoId( $value ) ) {
			return null;
		}

		return $value;

	}

	private function isValidVideoId( string $videoId ): bool {

		return 1 === preg_match( '/^[A-Za-z0-9_-]{11}$/', $videoId );

	}

	private function isValidMetadata( mixed $metadata ): bool {

		return is_array( $metadata )
			&& is_string( $metadata['title'] ?? null )
			&& '' !== trim( $metadata['title'] )
			&& is_string( $metadata['thumbnail'] ?? null )
			&& false !== filter_var( $metadata['thumbnail'], FILTER_VALIDATE_URL );

	}

}
