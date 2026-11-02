<?php

declare(strict_types=1);

namespace DMF\Media\Render;

use DMF\Media\Models\Video;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	/**
	 * Evita renderizar más de un modal cuando hay varios shortcodes en la página.
	 */
	private static bool $modalRendered = false;

	/**
	 * @param Video[] $videos
	 */
	public function render( array $videos ): string {

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

				<?php echo $this->renderCard( $video ); ?>

			<?php endforeach; ?>

		</div>


		<?php echo $this->renderModal(); ?>

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
						alt="<?php echo esc_attr( $video->title ); ?>"
						loading="lazy">

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

	private function renderModal(): string {

		if ( self::$modalRendered ) {
			return '';
		}

		self::$modalRendered = true;

		ob_start();
		?>

		<div
			id="dmf-video-modal"
			class="dmf-video-modal"
			role="dialog"
			aria-modal="true"
			aria-label="<?php echo esc_attr( 'Reproductor de vídeo' ); ?>"
			aria-hidden="true"
			tabindex="-1"
			hidden>

			<div class="dmf-video-modal__overlay"></div>

			<div class="dmf-video-modal__dialog">

				<button
					type="button"
					class="dmf-video-modal__close"
					aria-label="<?php echo esc_attr( 'Cerrar vídeo' ); ?>">

					&times;

				</button>

				<div
					id="dmf-video-player"
					class="dmf-video-player">

				</div>

			</div>

		</div>

		<?php

		return (string) ob_get_clean();

	}

}
