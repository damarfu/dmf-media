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
			'Configuración',
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
			ID del canal de YouTube del que se obtendrán los vídeos por defecto.
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
			Número máximo de vídeos mostrados cuando el shortcode no especifica el atributo <code>limit</code>.
		</p>

		<?php

	}

	public function renderPage(): void {

		?>

		<div class="wrap">

			<h1>DMF Media</h1>

			<form method="post" action="options.php">

				<?php

				settings_fields( 'dmf_media_group' );

				do_settings_sections( 'dmf-media' );

				submit_button();

				?>

			</form>

			<hr>

			<h2>📺 Uso del shortcode</h2>

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

						<td>Muestra los últimos vídeos del canal configurado.</td>

					</tr>

					<tr>

						<td><code>[dmf_media limit="1"]</code></td>

						<td>Muestra únicamente el último vídeo.</td>

					</tr>

					<tr>

						<td><code>[dmf_media limit="3"]</code></td>

						<td>Muestra los tres últimos vídeos.</td>

					</tr>

					<tr>

						<td><code>[dmf_media channel="UCxxxxxxxxxxxxxxxx"]</code></td>

						<td>Obtiene los vídeos de otro canal sin modificar la configuración global.</td>

					</tr>

				</tbody>

			</table>

			<p class="description" style="margin-top:12px;">
				Si no se especifica el atributo <code>channel</code>, DMF Media utilizará el canal configurado en esta página.
			</p>

			<div class="notice notice-info inline" style="margin-top:20px;">

				<p>

					<strong>Consejo:</strong>

					DMF Media proporciona el contenido (vídeos, miniaturas y títulos). El diseño y la maquetación dependen de tu tema o constructor de bloques.

				</p>

			</div>

		</div>

		<?php

	}

}