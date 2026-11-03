<?php
declare(strict_types=1);

use DMF\Media\Feed\YoutubeRssParser;

dmf_test(
	'YoutubeRssParser: convierte un feed en vídeos',
	static function (): void {
		$videos = ( new YoutubeRssParser() )->parse( dmf_fixture( 'feed.xml' ) );

		assert_count( 3, $videos );
		assert_same( 'Primero & directo', $videos[0]->title );
		assert_same( 'AAAAAAAAAAA', $videos[0]->videoId );
		assert_same( 'https://www.youtube.com/watch?v=AAAAAAAAAAA', $videos[0]->url );
		assert_same( 'https://i2.ytimg.com/vi/AAAAAAAAAAA/hqdefault.jpg', $videos[0]->thumbnail );
		assert_same( 'Descripción del primer vídeo', $videos[0]->description );
		assert_not_null( $videos[0]->publishedAt );
		assert_same( '2026-10-01', $videos[0]->publishedAt->format( 'Y-m-d' ) );
		assert_same( 'Segundo vídeo', $videos[1]->title );
		assert_same( 'Tercero', $videos[2]->title );
	}
);

dmf_test(
	'YoutubeRssParser: valida el feed y rechaza HTML',
	static function (): void {
		$parser = new YoutubeRssParser();

		assert_true( $parser->isValid( dmf_fixture( 'feed.xml' ) ) );
		assert_false( $parser->isValid( '<html><body>404</body></html>' ) );
		assert_false( $parser->isValid( '' ) );
		assert_same( [], $parser->parse( '<html><body>404</body></html>' ) );
	}
);

dmf_test(
	'YoutubeRssParser: rechaza un feed sin namespaces de YouTube',
	static function (): void {
		$xml = '<?xml version="1.0"?><feed xmlns="http://www.w3.org/2005/Atom"><entry><title>Sin media</title></entry></feed>';

		$parser = new YoutubeRssParser();

		assert_false( $parser->isValid( $xml ) );
		assert_same( [], $parser->parse( $xml ) );
	}
);

dmf_test(
	'YoutubeRssParser: omite entradas con ID inválido',
	static function (): void {
		$xml = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xmlns:yt="http://www.youtube.com/xml/schemas/2015">
	<entry>
		<yt:videoId>short</yt:videoId>
		<title>Inválido</title>
		<link rel="alternate" href="https://www.youtube.com/watch?v=short"/>
		<published>2026-10-01T10:00:00+00:00</published>
		<media:group>
			<media:thumbnail url="https://i2.ytimg.com/vi/short/hqdefault.jpg"/>
		</media:group>
	</entry>
	<entry>
		<yt:videoId>DDDDDDDDDDD</yt:videoId>
		<title>Válido</title>
		<link rel="alternate" href="https://www.youtube.com/watch?v=DDDDDDDDDDD"/>
		<published>2026-09-01T10:00:00+00:00</published>
		<media:group>
			<media:thumbnail url="https://i2.ytimg.com/vi/DDDDDDDDDDD/hqdefault.jpg"/>
		</media:group>
	</entry>
</feed>
XML;

		$videos = ( new YoutubeRssParser() )->parse( $xml );

		assert_count( 1, $videos );
		assert_same( 'DDDDDDDDDDD', $videos[0]->videoId );
	}
);
