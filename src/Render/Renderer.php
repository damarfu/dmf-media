<?php

declare(strict_types=1);

namespace DMF\Media\Render;

use DMF\Media\Models\Video;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	/**
	 * @param Video[] $videos
	 */
	public function render( array $videos ): string {

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

	private function renderCard( Video $video ): string {

		ob_start();

		?>

		<article class="dmf-video-card">

			<a
				class="dmf-video-link"
				href="<?php echo esc_url( $video->url ); ?>"
				target="_blank"
				rel="noopener noreferrer">

				<div class="dmf-video-thumb">

					<img
						src="<?php echo esc_url( $video->thumbnail ); ?>"
						alt="<?php echo esc_attr( $video->title ); ?>"
						loading="lazy">

					<div class="dmf-video-overlay">

						<div class="dmf-video-play">

							▶

						</div>

						<h3 class="dmf-video-title">

							<?php echo esc_html( $video->title ); ?>

						</h3>

					</div>

				</div>

			</a>

		</article>

		<?php

		return (string) ob_get_clean();

	}

}