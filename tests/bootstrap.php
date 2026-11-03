<?php
declare(strict_types=1);

/**
 * Bootstrap de las pruebas.
 *
 * Define las funciones de WordPress que usa el plugin con dobles sencillos,
 * para poder probar la lógica sin una instalación de WordPress.
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

if ( ! defined( 'DMF_MEDIA_FILE' ) ) {
	define( 'DMF_MEDIA_FILE', dirname( __DIR__ ) . '/dmf-media.php' );
}

if ( ! defined( 'DMF_MEDIA_VERSION' ) ) {
	define( 'DMF_MEDIA_VERSION', 'test' );
}

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

$GLOBALS['dmf_transients']      = [];
$GLOBALS['dmf_options']         = [];
$GLOBALS['dmf_http']            = [];
$GLOBALS['dmf_http_calls']      = [];
$GLOBALS['dmf_settings_errors'] = [];
$GLOBALS['dmf_deleted_options'] = [];
$GLOBALS['dmf_deleted_transients'] = [];
$GLOBALS['dmf_tests']           = [];
$GLOBALS['dmf_assertions']      = 0;

function dmf_reset_state(): void {

	$GLOBALS['dmf_transients']         = [];
	$GLOBALS['dmf_options']            = [];
	$GLOBALS['dmf_http']               = [];
	$GLOBALS['dmf_http_calls']         = [];
	$GLOBALS['dmf_settings_errors']    = [];
	$GLOBALS['dmf_deleted_options']    = [];
	$GLOBALS['dmf_deleted_transients'] = [];

}

class WP_Error {

	public function __construct(
		private string $code = '',
		private string $message = '',
	) {}

}

function is_wp_error( mixed $thing ): bool {

	return $thing instanceof WP_Error;

}

function get_transient( string $key ): mixed {

	return $GLOBALS['dmf_transients'][ $key ] ?? false;

}

function set_transient( string $key, mixed $value, int $expiration = 0 ): bool {

	$GLOBALS['dmf_transients'][ $key ] = $value;

	return true;

}

function delete_transient( string $key ): bool {

	unset( $GLOBALS['dmf_transients'][ $key ] );

	$GLOBALS['dmf_deleted_transients'][] = $key;

	return true;

}

function delete_option( string $name ): bool {

	$GLOBALS['dmf_deleted_options'][] = $name;

	return true;

}

function wp_parse_url( string $url, int $component = -1 ): array|false {

	return parse_url( $url, $component );

}

function add_query_arg( array $args, string $url ): string {

	return $url . '?' . http_build_query( $args );

}

/**
 * @param array<string, mixed> $args
 * @return array<string, mixed>
 */
function wp_remote_get( string $url, array $args = [] ): array {

	$GLOBALS['dmf_http_calls'][] = $url;

	foreach ( $GLOBALS['dmf_http'] as $needle => $response ) {
		if ( str_contains( $url, (string) $needle ) ) {
			return $response;
		}
	}

	return [
		'code'    => 404,
		'body'    => '<html>404</html>',
		'headers' => [],
	];

}

/**
 * @param array<string, mixed> $response
 */
function wp_remote_retrieve_response_code( array $response ): int {

	return (int) ( $response['code'] ?? 200 );

}

/**
 * @param array<string, mixed> $response
 */
function wp_remote_retrieve_header( array $response, string $header ): string {

	return (string) ( $response['headers'][ $header ] ?? '' );

}

/**
 * @param array<string, mixed> $response
 */
function wp_remote_retrieve_body( array $response ): string {

	return (string) ( $response['body'] ?? '' );

}

function get_option( string $name, mixed $default = false ): mixed {

	return $GLOBALS['dmf_options'][ $name ] ?? $default;

}

function sanitize_text_field( mixed $value ): string {

	return trim( strip_tags( (string) $value ) );

}

function add_settings_error( string $setting, string $code, string $message, string $type = 'error' ): void {

	$GLOBALS['dmf_settings_errors'][] = [
		'setting' => $setting,
		'code'    => $code,
		'message' => $message,
		'type'    => $type,
	];

}

function esc_html( mixed $text ): string {

	return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );

}

function esc_attr( mixed $text ): string {

	return esc_html( $text );

}

function esc_url( mixed $url ): string {

	return esc_html( $url );

}

function plugins_url( string $path = '', string $plugin = '' ): string {

	return 'https://example.test/wp-content/plugins/dmf-media/' . ltrim( $path, '/' );

}

function wp_enqueue_style( mixed ...$args ): void {}

function wp_enqueue_script( mixed ...$args ): void {}

/**
 * @param array<string, string> $extra
 */
function get_block_wrapper_attributes( array $extra = [] ): string {

	$class = $extra['class'] ?? '';

	return 'class="wp-block-dmf-media dmf-media-block ' . esc_attr( $class ) . '"';

}

/**
 * @param array<string, mixed> $pairs
 * @param mixed                $atts
 * @return array<string, mixed>
 */
function shortcode_atts( array $pairs, mixed $atts, string $shortcode = '' ): array {

	$atts = (array) $atts;
	$out  = [];

	foreach ( $pairs as $name => $default ) {
		$out[ $name ] = array_key_exists( $name, $atts ) ? $atts[ $name ] : $default;
	}

	return $out;

}

function dmf_fixture( string $name ): string {

	return (string) file_get_contents( __DIR__ . '/fixtures/' . $name );

}

/**
 * @return array<string, mixed>
 */
function dmf_response( string $body, int $code = 200, string $type = 'application/atom+xml' ): array {

	return [
		'code'    => $code,
		'body'    => $body,
		'headers' => [
			'content-type' => $type,
		],
	];

}

function dmf_set_channel_options( string $limit = '6' ): void {

	$GLOBALS['dmf_options']['dmf_media_settings'] = [
		'channel' => 'UCfoi0nD0pW_ipZOldgTrp4A',
		'limit'   => $limit,
	];

}

function dmf_set_feed(): void {

	$GLOBALS['dmf_http']['feeds/videos.xml?channel_id'] = dmf_response( dmf_fixture( 'feed.xml' ) );

}

function dmf_set_playlist_feed(): void {

	$GLOBALS['dmf_http']['feeds/videos.xml?playlist_id'] = dmf_response( dmf_fixture( 'feed.xml' ) );

}

function dmf_set_oembed( string $title = 'Manual' ): void {

	$GLOBALS['dmf_http']['oembed'] = dmf_response(
		(string) json_encode(
			[
				'title'         => $title,
				'thumbnail_url' => 'https://i.ytimg.com/vi/AAAAAAAAAAA/hqdefault.jpg',
			]
		),
		200,
		'application/json'
	);

}

/*
 * Mini framework de pruebas.
 */

function dmf_test( string $name, callable $callback ): void {

	$GLOBALS['dmf_tests'][] = [ $name, $callback ];

}

function dmf_assert( bool $condition, string $message ): void {

	$GLOBALS['dmf_assertions']++;

	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}

}

function assert_same( mixed $expected, mixed $actual, string $message = '' ): void {

	$message = '' !== $message ? $message : 'Valor inesperado';

	dmf_assert(
		$expected === $actual,
		$message . ': esperado ' . var_export( $expected, true ) . ', recibido ' . var_export( $actual, true )
	);

}

function assert_true( mixed $actual, string $message = 'Se esperaba true' ): void {

	dmf_assert( true === $actual, $message . ': recibido ' . var_export( $actual, true ) );

}

function assert_false( mixed $actual, string $message = 'Se esperaba false' ): void {

	dmf_assert( false === $actual, $message . ': recibido ' . var_export( $actual, true ) );

}

function assert_null( mixed $actual, string $message = 'Se esperaba null' ): void {

	dmf_assert( null === $actual, $message . ': recibido ' . var_export( $actual, true ) );

}

function assert_not_null( mixed $actual, string $message = 'Se esperaba un valor' ): void {

	dmf_assert( null !== $actual, $message );

}

function assert_count( int $expected, array $actual, string $message = '' ): void {

	$message = '' !== $message ? $message : 'Número de elementos inesperado';

	dmf_assert(
		$expected === count( $actual ),
		$message . ': esperado ' . $expected . ', recibido ' . count( $actual )
	);

}

function assert_contains( string $needle, string $haystack, string $message = '' ): void {

	$message = '' !== $message ? $message : 'Texto no encontrado';

	dmf_assert(
		str_contains( $haystack, $needle ),
		$message . ': no se encontró «' . $needle . '»'
	);

}

function assert_not_contains( string $needle, string $haystack, string $message = '' ): void {

	$message = '' !== $message ? $message : 'Texto inesperado';

	dmf_assert(
		! str_contains( $haystack, $needle ),
		$message . ': se encontró «' . $needle . '»'
	);

}

function assert_throws( callable $callback, string $class, string $message = '' ): void {

	$message = '' !== $message ? $message : 'No se lanzó la excepción esperada';

	try {
		$callback();
	} catch ( Throwable $exception ) {
		dmf_assert(
			$exception instanceof $class,
			$message . ': se lanzó ' . $exception::class
		);

		return;
	}

	dmf_assert( false, $message . ': no se lanzó ninguna excepción' );

}
