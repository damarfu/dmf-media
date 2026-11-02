<?php

declare(strict_types=1);

namespace DMF\Media\Resolver;

use DMF\Media\Models\Video;

defined( 'ABSPATH' ) || exit;

/**
 * Resultado tipado de una consulta de contenido.
 *
 * El error espera ser un mensaje ya redactado para mostrar al usuario; los
 * fallos de red no se propagan como excepciones.
 */
final class MediaResult {

	/**
	 * @param Video[] $videos
	 */
	private function __construct(
		public readonly array $videos,
		public readonly ?string $error,
	) {}

	/**
	 * @param Video[] $videos
	 */
	public static function success( array $videos ): self {

		return new self( $videos, null );

	}

	public static function failure( string $message ): self {

		return new self( [], $message );

	}

	public function hasError(): bool {

		return null !== $this->error;

	}

}
