<?php

declare(strict_types=1);

namespace DMF\Media\Models;

use DateTimeImmutable;
use InvalidArgumentException;

defined( 'ABSPATH' ) || exit;

/**
 * Representa un vídeo del plugin.
 */
final class Video {

	public function __construct(
		public readonly string $title,
		public readonly string $url,
		public readonly string $thumbnail,
		public readonly DateTimeImmutable $publishedAt,
		public readonly string $description = '',
	) {

		if ( trim( $this->title ) === '' ) {
			throw new InvalidArgumentException( 'Video title cannot be empty.' );
		}

		if ( filter_var( $this->url, FILTER_VALIDATE_URL ) === false ) {
			throw new InvalidArgumentException( 'Invalid video URL.' );
		}

		if ( filter_var( $this->thumbnail, FILTER_VALIDATE_URL ) === false ) {
			throw new InvalidArgumentException( 'Invalid thumbnail URL.' );
		}

	}

}