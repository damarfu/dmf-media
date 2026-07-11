<?php

namespace DMF\Media\Core;

defined( 'ABSPATH' ) || exit;

class Plugin {

	/**
	 * Arranque del plugin.
	 */
	public static function boot(): void {

		add_action( 'init', [ self::class, 'init' ] );

	}

	/**
	 * Inicialización.
	 */
	public static function init(): void {

		// De momento no hacemos nada.

	}

}