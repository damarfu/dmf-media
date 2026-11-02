<?php

declare(strict_types=1);

namespace DMF\Media\Render;

defined( 'ABSPATH' ) || exit;

/**
 * Modal global de reproducción.
 *
 * Se imprime una sola vez desde wp_footer, como hijo directo de <body>, para
 * que ningún contenedor del tema (transform, overflow, z-index) lo recorte o
 * lo desplace. El shortcode y el bloque solo marcan que hace falta.
 */
final class Modal {

	private static bool $needed  = false;
	private static bool $printed = false;

	public static function require(): void {

		self::$needed = true;

	}

	public static function isNeeded(): bool {

		return self::$needed;

	}

	public static function render(): void {

		if ( self::$printed || ! self::$needed ) {
			return;
		}

		self::$printed = true;

		echo self::markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}

	public static function markup(): string {

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
