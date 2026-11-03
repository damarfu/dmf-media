<?php
/**
 * Render del bloque DMF Media.
 *
 * @var array    $attributes Atributos del bloque.
 * @var string   $content    Contenido interno.
 * @var WP_Block $block      Instancia del bloque.
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $attributes ) || ! is_array( $attributes ) ) {
	$attributes = [];
}

echo \DMF\Media\Blocks\MediaBlock::render( $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
