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

		$videos = $feed->getVideos();

		ob_start();

		if ( empty( $videos ) ) {

			echo '<p>No se encontraron vídeos.</p>';

			return (string) ob_get_clean();

		}

		echo '<div class="dmf-media">';

		foreach ( $videos as $video ) {

			?>

			<article class="dmf-video">

				<h3><?php echo esc_html( $video->title ); ?></h3>

				<p>
					<?php
					echo esc_html(
						$video->publishedAt->format( 'd/m/Y' )
					);
					?>
				</p>

			</article>

			<?php

		}

		echo '</div>';

		return (string) ob_get_clean();

	}

}