<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

use DateTimeImmutable;
use DMF\Media\Models\Video;
use DMF\Media\Support\YoutubeId;
use SimpleXMLElement;
use Throwable;

defined( 'ABSPATH' ) || exit;

/**
 * Convierte entradas RSS de YouTube en objetos Video.
 */
final class YoutubeRssParser {

	/**
	 * @return array<int, Video>
	 */
	public function parse( string $xmlString ): array {

		$xml = $this->load( $xmlString );

		if ( null === $xml ) {
			return [];
		}

		$namespaces = $xml->getNamespaces( true );

		if ( empty( $namespaces['media'] ) || empty( $namespaces['yt'] ) ) {
			return [];
		}

		$videos = [];

		foreach ( $xml->entry as $entry ) {
			$video = $this->parseEntry( $entry, $namespaces );

			if ( null !== $video ) {
				$videos[] = $video;
			}
		}

		return $videos;

	}

	/**
	 * Comprueba que el XML es válido y que es un feed de YouTube.
	 */
	public function isValid( string $xmlString ): bool {

		$xml = $this->load( $xmlString );

		if ( null === $xml ) {
			return false;
		}

		$namespaces = $xml->getNamespaces( true );

		return ! empty( $namespaces['media'] ) && ! empty( $namespaces['yt'] );

	}

	private function load( string $xmlString ): ?SimpleXMLElement {

		if ( '' === trim( $xmlString ) ) {
			return null;
		}

		$previousUseErrors = libxml_use_internal_errors( true );
		$xml               = simplexml_load_string( $xmlString );

		libxml_clear_errors();
		libxml_use_internal_errors( $previousUseErrors );

		if ( false === $xml ) {
			return null;
		}

		if ( 'feed' !== $xml->getName() ) {
			return null;
		}

		return $xml;

	}

	/**
	 * @param array<string, string> $namespaces
	 */
	private function parseEntry( SimpleXMLElement $entry, array $namespaces ): ?Video {

		$mediaGroup = $entry->children( $namespaces['media'] )->group;
		$youtube    = $entry->children( $namespaces['yt'] );
		$thumbnail  = $mediaGroup->thumbnail->attributes()->url;
		$link       = $entry->link['href'];

		$title        = trim( (string) $entry->title );
		$videoId      = YoutubeId::video( trim( (string) $youtube->videoId ) );
		$url          = trim( (string) $link );
		$thumbnailUrl = trim( (string) $thumbnail );
		$published    = trim( (string) $entry->published );

		if ( '' === $title || null === $videoId || '' === $url || '' === $thumbnailUrl || '' === $published ) {
			return null;
		}

		try {
			return new Video(
				title: $title,
				videoId: $videoId,
				url: $url,
				thumbnail: $thumbnailUrl,
				publishedAt: new DateTimeImmutable( $published ),
				description: (string) $mediaGroup->description
			);
		} catch ( Throwable ) {
			return null;
		}

	}

}
