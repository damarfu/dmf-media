<?php
declare(strict_types=1);

final class DmfFakeWpdb {

	public string $options = 'wp_options';

	public function esc_like( string $text ): string {

		return addcslashes( $text, '_%\\' );

	}

	public function prepare( string $query, mixed ...$args ): string {

		return $query;

	}

	/**
	 * @return array<int, string>
	 */
	public function get_col( string $query ): array {

		return $GLOBALS['dmf_transient_options'] ?? [];

	}

}

dmf_test(
	'uninstall.php: borra los ajustes y los transients',
	static function (): void {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', 'dmf-media/dmf-media.php' );
		}

		$GLOBALS['wpdb']                  = new DmfFakeWpdb();
		$GLOBALS['dmf_transient_options'] = [
			'_transient_dmf_media_feed_v2_abc',
			'_transient_dmf_media_video_v1_def',
		];

		require dirname( __DIR__ ) . '/uninstall.php';

		assert_same( [ 'dmf_media_settings' ], $GLOBALS['dmf_deleted_options'] );
		assert_same(
			[ 'dmf_media_feed_v2_abc', 'dmf_media_video_v1_def' ],
			$GLOBALS['dmf_deleted_transients']
		);
	}
);
