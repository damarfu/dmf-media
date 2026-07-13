<?php

declare(strict_types=1);

namespace DMF\Media\Feed;

defined( 'ABSPATH' ) || exit;

/**
 * Servicio encargado de obtener vídeos desde YouTube.
 */
final class YoutubeFeed {

	/**
	 * Channel ID de YouTube.
	 */
	private string $channelId;

	/**
	 * Constructor.
	 *
	 * @param string $channelId Channel ID del canal.
	 */
	public function __construct( string $channelId ) {

		$this->channelId = trim( $channelId );

	}

	/**
	 * Devuelve el Channel ID.
	 */
	public function getChannelId(): string {

		return $this->channelId;

	}

}