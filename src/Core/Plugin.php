<?php

declare(strict_types=1);

namespace DMF\Media\Core;

use DMF\Media\Admin\Settings;
use DMF\Media\Render\Modal;
use DMF\Media\Shortcodes\MediaShortcode;

defined( 'ABSPATH' ) || exit;

final class Plugin {

	public static function boot(): void {

		$settings = new Settings();

		add_action(
			'init',
			static function () {

				(new MediaShortcode())->register();

			}
		);

		add_action(
			'admin_menu',
			[ $settings, 'addMenu' ]
		);

		add_action(
			'admin_init',
			[ $settings, 'registerSettings' ]
		);

		add_action(
			'wp_enqueue_scripts',
			[ self::class, 'maybeEnqueueAssets' ]
		);

		/*
		 * El modal se imprime al final del body, una sola vez, fuera del HTML
		 * del shortcode o del bloque. Así ningún contenedor del tema puede
		 * recortarlo con overflow o atraparlo en su contexto de apilamiento.
		 */
		add_action(
			'wp_footer',
			[ Modal::class, 'render' ],
			10
		);

	}

	/**
	 * Encola los assets cuando la página actual contiene el shortcode o el bloque.
	 *
	 * Es solo una optimización: si el contenido se genera de forma dinámica
	 * (widgets, plantillas, builders), cada shortcode los encola al renderizar.
	 */
	public static function maybeEnqueueAssets(): void {

		if ( ! self::currentPageUsesMedia() ) {
			return;
		}

		self::enqueueAssets();

	}

	public static function enqueueAssets(): void {

		wp_enqueue_style(
			'dmf-media',
			plugins_url( 'assets/css/frontend.css', DMF_MEDIA_FILE ),
			[],
			DMF_MEDIA_VERSION
		);

		wp_enqueue_script(
			'dmf-media',
			plugins_url( 'assets/js/frontend.js', DMF_MEDIA_FILE ),
			[],
			DMF_MEDIA_VERSION,
			true
		);

	}

	private static function currentPageUsesMedia(): bool {

		$post = get_post();

		if ( ! $post instanceof \WP_Post ) {
			return false;
		}

		if ( has_shortcode( $post->post_content, 'dmf_media' ) ) {
			return true;
		}

		return function_exists( 'has_block' ) && has_block( 'dmf/media', $post );

	}

}
