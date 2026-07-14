<?php

declare(strict_types=1);

namespace DMF\Media\Render;

use DMF\Media\Feed\YoutubeFeed;
use DMF\Media\Models\Video;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	public function render(): string {

		$feed = new YoutubeFeed(
			'UCfoi0nD0pW_ipZOldgTrp4A'
		);

		$videos = $feed->getVideos();

		ob_start();

		if ( empty( $videos ) ) {
			?>
			<p>No se encontraron vídeos.</p>
			<?php

			return (string) ob_get_clean();
		}

		?>
		<div class="dmf-media-grid">

			<?php foreach ( $videos as $video ) : ?>

				<?php echo $this->renderCard( $video ); ?>

			<?php endforeach; ?>

		</div>
		<?php

		return (string) ob_get_clean();

	}

	/**
	 * Renderiza una tarjeta de vídeo.
	 */
	private function renderCard( Video $video ): string {

		ob_start();
		?>

		<article class="dmf-video-card">

			<div class="dmf-video-thumb">

				<a
					href="<?php echo esc_url( $video->url ); ?>"
					target="_blank"
					rel="noopener noreferrer">

					<img
						src="<?php echo esc_url( $video->thumbnail ); ?>"
						alt="<?php echo esc_attr( $video->title ); ?>"
						loading="lazy">

				</a>

			</div>

			<div class="dmf-video-content">

				<h3 class="dmf-video-title">

					<a
						href="<?php echo esc_url( $video->url ); ?>"
						target="_blank"
						rel="noopener noreferrer">

						<?php echo esc_html( $video->title ); ?>

					</a>

				</h3>

				<p class="dmf-video-date">

					<?php echo esc_html( $video->publishedAt->format( 'd/m/Y' ) ); ?>

				</p>

				<p>

					<a
						class="dmf-video-button"
						href="<?php echo esc_url( $video->url ); ?>"
						target="_blank"
						rel="noopener noreferrer">

						▶ Ver vídeo

					</a>

				</p>

			</div>

		</article>

		<?php

		return (string) ob_get_clean();

	}

}