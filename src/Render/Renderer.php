<?php

declare(strict_types=1);

namespace DMF\Media\Render;

use DMF\Media\Models\Video;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	/**
	 * @param array<int, Video> $videos
	 */
	public function render( array $videos ): string {

		Modal::require();

		ob_start();

		if ( empty( $videos ) ) {
			?>

			<p class="dmf-media-message">No hay vídeos disponibles.</p>

			<?php

			return (string) ob_get_clean();
		}

		$wrapperClass = count( $videos ) === 1
			? 'dmf-media-single'
			: 'dmf-media-grid';

		?>

		<div class="<?php echo esc_attr( $wrapperClass ); ?>">

			<?php foreach ( $videos as $video ) : ?>

				<?php echo $this->renderCard( $video ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

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
				data-video-id="<?php echo esc_attr( $video->videoId ); ?>">

				<div class="dmf-video-thumb">

					<img
						src="<?php echo esc_url( $video->thumbnail ); ?>"
						alt=""
						loading="lazy"
						decoding="async">

					<div class="dmf-video-overlay">

						<div class="dmf-video-play">

							<svg
								viewBox="0 0 24 24"
								aria-hidden="true"
								focusable="false">

								<path
									d="M8 5v14l11-7z"
									fill="currentColor" />

							</svg>

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
