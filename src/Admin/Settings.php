<?php

declare(strict_types=1);

namespace DMF\Media\Admin;

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
			'dmf_media_settings'
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

	public function channelField(): void {

		$options = get_option( 'dmf_media_settings', [] );

		?>

		<input
			type="text"
			class="regular-text"
			name="dmf_media_settings[channel]"
			value="<?php echo esc_attr( $options['channel'] ?? '' ); ?>"
			placeholder="UCxxxxxxxxxxxxxxxx">

		<p class="description">
			El identificador único del canal de YouTube del que se obtendrán los vídeos por defecto. Debe comenzar por <code>UC</code>, por ejemplo: <code>UCfoi0nD0pW_ipZOldgTrp4A</code>.
		</p>

		<p class="description">
			Puedes localizarlo en una URL de YouTube Studio o de canal que contenga <code>/channel/UC...</code>. Actualmente, <code>@nombrecanal</code> no está admitido como Channel ID.
		</p>

		<?php

	}

	public function limitField(): void {

		$options = get_option( 'dmf_media_settings', [] );

		?>

		<input
			type="number"
			min="1"
			max="20"
			name="dmf_media_settings[limit]"
			value="<?php echo esc_attr( $options['limit'] ?? 6 ); ?>">

		<p class="description">
			Número de vídeos que se muestran por defecto, entre 1 y 20. El atributo <code>limit</code> del shortcode puede sobrescribir este valor.
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

						<td>Muestra tres vídeos. El atributo <code>limit</code> permite definir entre 1 y 20 vídeos para ese shortcode.</td>

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
				El feed RSS público de YouTube proporciona actualmente un máximo de 15 vídeos por playlist. El atributo <code>limit</code> puede reducir ese número, pero no puede mostrar más vídeos de los que proporciona YouTube mediante RSS.
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
