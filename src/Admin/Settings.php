<?php

declare(strict_types=1);

namespace DMF\Media\Admin;

use DMF\Media\Resolver\MediaResolver;
use DMF\Media\Support\YoutubeId;

defined( 'ABSPATH' ) || exit;

final class Settings {

	public function addMenu(): void {

		add_options_page(
			'DMF Media',
			'DMF Media',
			'manage_options',
			'dmf-media',
			[ $this, 'renderPage' ]
		);

	}

	public function registerSettings(): void {

		register_setting(
			'dmf_media_group',
			'dmf_media_settings',
			[
				'type'              => 'array',
				'default'           => [
					'channel' => '',
					'limit'   => MediaResolver::DEFAULT_LIMIT,
				],
				'sanitize_callback' => [ $this, 'sanitizeSettings' ],
			]
		);

		add_settings_section(
			'dmf_media_main',
			'Configuración predeterminada',
			'__return_false',
			'dmf-media'
		);

		add_settings_field(
			'channel',
			'Channel ID',
			[ $this, 'channelField' ],
			'dmf-media',
			'dmf_media_main'
		);

		add_settings_field(
			'limit',
			'Número de vídeos',
			[ $this, 'limitField' ],
			'dmf-media',
			'dmf_media_main'
		);

	}

	/**
	 * Valida y normaliza los ajustes antes de guardarlos.
	 *
	 * Si el Channel ID no es válido se conserva el valor anterior y se muestra
	 * un aviso, para no perder la configuración por un error de tecleo.
	 *
	 * @param mixed $input Valor enviado desde el formulario.
	 * @return array<string, mixed>
	 */
	public function sanitizeSettings( mixed $input ): array {

		$input   = is_array( $input ) ? $input : [];
		$current = get_option( 'dmf_media_settings', [] );
		$current = is_array( $current ) ? $current : [];

		$channel = isset( $current['channel'] ) && is_string( $current['channel'] )
			? $current['channel']
			: '';

		if ( array_key_exists( 'channel', $input ) ) {
			if ( ! is_string( $input['channel'] ) ) {
				add_settings_error(
					'dmf_media_settings',
					'dmf_media_invalid_channel',
					'El Channel ID no es válido. Debe empezar por «UC» y tener 24 caracteres, por ejemplo UCfoi0nD0pW_ipZOldgTrp4A.'
				);
			} else {
				$channelRaw = trim( sanitize_text_field( $input['channel'] ) );

				if ( '' === $channelRaw ) {
					$channel = '';
				} else {
					$channelId = YoutubeId::channel( $channelRaw );

					if ( null === $channelId ) {
						add_settings_error(
							'dmf_media_settings',
							'dmf_media_invalid_channel',
							'El Channel ID no es válido. Debe empezar por «UC» y tener 24 caracteres, por ejemplo UCfoi0nD0pW_ipZOldgTrp4A.'
						);
					} else {
						$channel = $channelId;
					}
				}
			}
		}

		$limitRaw = $input['limit'] ?? null;
		$limit    = is_numeric( $limitRaw )
			? (int) $limitRaw
			: (int) ( $current['limit'] ?? MediaResolver::DEFAULT_LIMIT );

		return [
			'channel' => $channel,
			'limit'   => max( 1, min( MediaResolver::MAX_LIMIT, $limit ) ),
		];

	}

	public function channelField(): void {

		$options = get_option( 'dmf_media_settings', [] );
		$options = is_array( $options ) ? $options : [];
		$channel = isset( $options['channel'] ) && is_string( $options['channel'] ) ? $options['channel'] : '';

		?>

		<input
			type="text"
			class="regular-text"
			name="dmf_media_settings[channel]"
			value="<?php echo esc_attr( $channel ); ?>"
			placeholder="UCxxxxxxxxxxxxxxxx">

		<p class="description">
			El identificador único del canal de YouTube del que se obtendrán los vídeos por defecto. Debe comenzar por <code>UC</code>, por ejemplo: <code>UCfoi0nD0pW_ipZOldgTrp4A</code>.
		</p>

		<p class="description">
			Puedes pegar el Channel ID o una URL de canal que contenga <code>/channel/UC...</code>. Actualmente, <code>@nombrecanal</code> no está admitido.
		</p>

		<?php

	}

	public function limitField(): void {

		$options = get_option( 'dmf_media_settings', [] );
		$options = is_array( $options ) ? $options : [];
		$limit   = isset( $options['limit'] ) && is_numeric( $options['limit'] ) ? (int) $options['limit'] : MediaResolver::DEFAULT_LIMIT;

		?>

		<input
			type="number"
			min="1"
			max="<?php echo esc_attr( (string) MediaResolver::MAX_LIMIT ); ?>"
			name="dmf_media_settings[limit]"
			value="<?php echo esc_attr( (string) $limit ); ?>">

		<p class="description">
			Número de vídeos que se muestran por defecto, entre 1 y <?php echo esc_html( (string) MediaResolver::MAX_LIMIT ); ?>. El atributo <code>limit</code> del shortcode puede sobrescribir este valor.
		</p>

		<?php

	}

	public function renderPage(): void {

		?>

		<div class="wrap">

			<h1>DMF Media</h1>

			<p>
				DMF Media permite mostrar los últimos vídeos de un canal de YouTube, los vídeos de una playlist pública o un vídeo concreto mediante las tarjetas y el reproductor modal propios del plugin.
			</p>

			<form method="post" action="options.php">

				<?php

				settings_fields( 'dmf_media_group' );

				do_settings_sections( 'dmf-media' );

				submit_button();

				?>

			</form>

			<hr>

			<h2>Uso mediante shortcode</h2>

			<h3>Feed del canal</h3>

			<table class="widefat striped" style="max-width:900px">

				<thead>

					<tr>
						<th>Ejemplo</th>
						<th>Descripción</th>
					</tr>

				</thead>

				<tbody>

					<tr>

						<td><code>[dmf_media]</code></td>

						<td>Muestra los últimos vídeos del Channel ID configurado en esta página.</td>

					</tr>

					<tr>

						<td><code>[dmf_media limit="3"]</code></td>

						<td>Muestra tres vídeos. El atributo <code>limit</code> permite definir entre 1 y 15 vídeos para ese shortcode.</td>

					</tr>

					<tr>

						<td><code>[dmf_media offset="1" limit="6"]</code></td>

						<td>Omite el vídeo más reciente y muestra los seis siguientes. También puedes usar <code>offset="2"</code> para omitir los dos más recientes. Solo trabaja con los vídeos disponibles en el RSS.</td>

					</tr>

					<tr>

						<td><code>[dmf_media channel="UCxxxxxxxxxxxxxxxx"]</code></td>

						<td>Obtiene los vídeos de otro Channel ID sin modificar la configuración predeterminada.</td>

					</tr>

				</tbody>

			</table>

			<h3>Playlist pública</h3>

			<table class="widefat striped" style="max-width:900px">

				<thead>

					<tr>
						<th>Ejemplo</th>
						<th>Descripción</th>
					</tr>

				</thead>

				<tbody>

					<tr>

						<td><code>[dmf_media playlist="PLxxxxxxxx"]</code></td>

						<td>Muestra los vídeos de una playlist pública mediante su Playlist ID.</td>

					</tr>

					<tr>

						<td><code>[dmf_media playlist="https://www.youtube.com/playlist?list=PLxxxxxxxx"]</code></td>

						<td>Muestra una playlist pública mediante su URL estándar. DMF Media utiliza el RSS público de YouTube y no requiere API key.</td>

					</tr>

				</tbody>

			</table>

			<p class="description" style="margin-top:12px; max-width:900px;">
				El feed RSS público de YouTube proporciona actualmente un máximo de 15 vídeos, tanto en canales como en playlists. El atributo <code>limit</code> puede reducir ese número, pero no puede mostrar más vídeos de los que proporciona YouTube mediante RSS.
			</p>

			<h3>Vídeo manual</h3>

			<table class="widefat striped" style="max-width:900px">

				<thead>

					<tr>
						<th>Ejemplo</th>
						<th>Descripción</th>
					</tr>

				</thead>

				<tbody>

					<tr>

						<td><code>[dmf_media video="VIDEO_ID"]</code></td>

						<td>Muestra un vídeo concreto mediante su ID de YouTube.</td>

					</tr>

					<tr>

						<td><code>[dmf_media video="https://www.youtube.com/watch?v=VIDEO_ID"]</code></td>

						<td>Muestra un vídeo concreto mediante su URL. También se admiten las URL <code>youtu.be</code> y <code>shorts</code>.</td>

					</tr>

				</tbody>

			</table>

			<div class="notice notice-warning inline" style="margin-top:20px; max-width:900px;">

				<p>

					<strong>Importante:</strong> las fuentes tienen esta prioridad: <code>video</code> &gt; <code>playlist</code> &gt; <code>channel</code>. Cuando se utiliza <code>video</code>, DMF Media muestra únicamente ese vídeo e ignora los atributos <code>playlist</code>, <code>channel</code> y <code>limit</code>.

				</p>

				<p>

					Por ejemplo, <code>[dmf_media video="VIDEO_ID" playlist="PLxxxxxxxx" channel="UCxxxxxxxx"]</code> muestra solamente el vídeo indicado en <code>video</code>.

				</p>

			</div>

			<p class="description" style="margin-top:12px; max-width:900px;">
				El atributo <code>offset</code> funciona con feeds de canal y playlists. Se aplica antes que <code>limit</code> y no afecta al modo de vídeo manual. YouTube proporciona actualmente un máximo de 15 vídeos mediante RSS, tanto en canales como en playlists.
			</p>

			<div class="notice notice-info inline" style="margin-top:20px;">

				<p>

					<strong>Consejo:</strong>

					El tema utilizado y herramientas como GenerateBlocks pueden controlar aspectos externos de la tarjeta, como anchuras, espaciados y su contexto visual.

				</p>

			</div>

		</div>

		<?php

	}

}
