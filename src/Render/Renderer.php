<?php

declare(strict_types=1);

namespace DMF\Media\Render;

use DMF\Media\Feed\YoutubeFeed;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	public function render(): string {

		$feed = new YoutubeFeed(
			'UCfoi0nD0pW_ipZOldgTrp4A'
		);

		$xml = $feed->fetch();

		ob_start();

		if ( ! $xml ) {

			echo '<p>No se ha podido leer el feed.</p>';

		} else {

			echo '<h3>Feed leído correctamente ✅</h3>';
			echo '<p>Título del canal: ' . esc_html( (string) $xml->title ) . '</p>';
			echo '<p>Entradas encontradas: ' . count( $xml->entry ) . '</p>';

		}

		return (string) ob_get_clean();

	}

}