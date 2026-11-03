<?php
declare(strict_types=1);

use DMF\Media\Admin\Settings;

dmf_test(
	'Settings: normaliza la URL del canal y el límite',
	static function (): void {
		$GLOBALS['dmf_options']['dmf_media_settings'] = [
			'channel' => 'UCfoi0nD0pW_ipZOldgTrp4A',
			'limit'   => 3,
		];

		$settings = new Settings();
		$result   = $settings->sanitizeSettings(
			[
				'channel' => 'https://www.youtube.com/channel/UCfoi0nD0pW_ipZOldgTrp4A',
				'limit'   => '9',
			]
		);

		assert_same( 'UCfoi0nD0pW_ipZOldgTrp4A', $result['channel'] );
		assert_same( 9, $result['limit'] );
	}
);

dmf_test(
	'Settings: un canal inválido conserva el anterior y avisa',
	static function (): void {
		$GLOBALS['dmf_options']['dmf_media_settings'] = [
			'channel' => 'UCfoi0nD0pW_ipZOldgTrp4A',
			'limit'   => 3,
		];

		$settings = new Settings();
		$result   = $settings->sanitizeSettings(
			[
				'channel' => '@mal',
				'limit'   => '3',
			]
		);

		assert_same( 'UCfoi0nD0pW_ipZOldgTrp4A', $result['channel'] );
		assert_count( 1, $GLOBALS['dmf_settings_errors'] );
		assert_same( 'dmf_media_invalid_channel', $GLOBALS['dmf_settings_errors'][0]['code'] );
	}
);

dmf_test(
	'Settings: un valor no string de canal no rompe y avisa',
	static function (): void {
		$GLOBALS['dmf_options']['dmf_media_settings'] = [
			'channel' => 'UCfoi0nD0pW_ipZOldgTrp4A',
			'limit'   => 3,
		];

		$settings = new Settings();
		$result   = $settings->sanitizeSettings(
			[
				'channel' => [ 'x' ],
				'limit'   => '4',
			]
		);

		assert_same( 'UCfoi0nD0pW_ipZOldgTrp4A', $result['channel'] );
		assert_same( 4, $result['limit'] );
		assert_count( 1, $GLOBALS['dmf_settings_errors'] );
	}
);

dmf_test(
	'Settings: respeta el canal si no viene en el formulario',
	static function (): void {
		$GLOBALS['dmf_options']['dmf_media_settings'] = [
			'channel' => 'UCfoi0nD0pW_ipZOldgTrp4A',
			'limit'   => 3,
		];

		$result = ( new Settings() )->sanitizeSettings( [ 'limit' => '4' ] );

		assert_same( 'UCfoi0nD0pW_ipZOldgTrp4A', $result['channel'] );
		assert_same( 4, $result['limit'] );
	}
);

dmf_test(
	'Settings: vaciar el canal es intencionado y no avisa',
	static function (): void {
		$GLOBALS['dmf_options']['dmf_media_settings'] = [
			'channel' => 'UCfoi0nD0pW_ipZOldgTrp4A',
			'limit'   => 3,
		];

		$result = ( new Settings() )->sanitizeSettings(
			[
				'channel' => '',
				'limit'   => '3',
			]
		);

		assert_same( '', $result['channel'] );
		assert_count( 0, $GLOBALS['dmf_settings_errors'] );
	}
);

dmf_test(
	'Settings: límites fuera de rango',
	static function (): void {
		$GLOBALS['dmf_options']['dmf_media_settings'] = [
			'channel' => 'UCfoi0nD0pW_ipZOldgTrp4A',
			'limit'   => 3,
		];

		$settings = new Settings();

		assert_same( 1, $settings->sanitizeSettings( [ 'limit' => '0' ] )['limit'] );
		assert_same( 15, $settings->sanitizeSettings( [ 'limit' => '99' ] )['limit'] );
		assert_same( 3, $settings->sanitizeSettings( [ 'limit' => 'abc' ] )['limit'] );
	}
);

dmf_test(
	'Settings: una entrada no array no rompe',
	static function (): void {
		$GLOBALS['dmf_options']['dmf_media_settings'] = [
			'channel' => 'UCfoi0nD0pW_ipZOldgTrp4A',
			'limit'   => 3,
		];

		$result = ( new Settings() )->sanitizeSettings( 'basura' );

		assert_same( 'UCfoi0nD0pW_ipZOldgTrp4A', $result['channel'] );
		assert_same( 3, $result['limit'] );
	}
);
