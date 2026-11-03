<?php
/**
 * Limpieza al desinstalar DMF Media.
 *
 * Borra la configuración y los transients del plugin. No se ejecuta al
 * desactivarlo: solo cuando WordPress elimina el plugin.
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'dmf_media_settings' );

global $wpdb;

$like  = $wpdb->esc_like( '_transient_dmf_media_' ) . '%';
$names = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
		$like
	)
);

if ( ! is_array( $names ) ) {
	return;
}

foreach ( $names as $name ) {
	$key = substr( $name, strlen( '_transient_' ) );

	if ( '' !== $key ) {
		delete_transient( $key );
	}
}
