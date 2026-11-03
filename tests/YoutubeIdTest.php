<?php
declare(strict_types=1);

use DMF\Media\Support\YoutubeId;

dmf_test(
	'YoutubeId: canal por ID y por URL',
	static function (): void {
		$id = 'UCfoi0nD0pW_ipZOldgTrp4A';

		assert_same( $id, YoutubeId::channel( $id ) );
		assert_same( $id, YoutubeId::channel( 'https://www.youtube.com/channel/' . $id ) );
		assert_same( $id, YoutubeId::channel( 'https://m.youtube.com/channel/' . $id ) );
		assert_same( $id, YoutubeId::channel( 'youtube.com/channel/' . $id ) );
		assert_null( YoutubeId::channel( '@midominio' ) );
		assert_null( YoutubeId::channel( 'UCabc' ) );
		assert_null( YoutubeId::channel( 'https://studio.youtube.com/channel/' . $id ) );
	}
);

dmf_test(
	'YoutubeId: playlist por ID y por URL',
	static function (): void {
		$playlist = 'PLabcdefghijklmnop';

		assert_same( $playlist, YoutubeId::playlist( $playlist ) );
		assert_same( $playlist, YoutubeId::playlist( 'https://www.youtube.com/playlist?list=' . $playlist ) );
		assert_same( $playlist, YoutubeId::playlist( 'https://m.youtube.com/playlist?list=' . $playlist ) );
		assert_same( $playlist, YoutubeId::playlist( 'https://music.youtube.com/playlist?list=' . $playlist ) );
		assert_same( $playlist, YoutubeId::playlist( 'https://www.youtube.com/watch?v=AAAAAAAAAAA&list=' . $playlist ) );
		assert_same( $playlist, YoutubeId::playlist( 'https://www.youtube.com/embed/videoseries?list=' . $playlist ) );
		assert_same( 'UUfoi0nD0pW_ipZOldgTrp4A', YoutubeId::playlist( 'UUfoi0nD0pW_ipZOldgTrp4A' ) );
		assert_same( 'OLAK5uy_abcdefghijklmnopqrstuvwxyz', YoutubeId::playlist( 'OLAK5uy_abcdefghijklmnopqrstuvwxyz' ) );
		assert_null( YoutubeId::playlist( 'PLnope' ) );
		assert_null( YoutubeId::playlist( 'UCfoi0nD0pW_ipZOldgTrp4A' ) );
	}
);

dmf_test(
	'YoutubeId: vídeo por ID y por URL',
	static function (): void {
		$id = 'AAAAAAAAAAA';

		assert_same( $id, YoutubeId::video( $id ) );
		assert_same( $id, YoutubeId::video( 'https://www.youtube.com/watch?v=' . $id . '&t=10' ) );
		assert_same( $id, YoutubeId::video( 'https://youtu.be/' . $id . '?t=5' ) );
		assert_same( $id, YoutubeId::video( 'https://www.youtube.com/shorts/' . $id ) );
		assert_same( $id, YoutubeId::video( 'https://www.youtube.com/embed/' . $id ) );
		assert_same( $id, YoutubeId::video( 'https://www.youtube.com/live/' . $id ) );
		assert_same( $id, YoutubeId::video( 'https://m.youtube.com/watch?v=' . $id ) );
		assert_null( YoutubeId::video( 'https://vimeo.com/123' ) );
		assert_null( YoutubeId::video( 'AAAAAAAAAAAA' ) );
	}
);
