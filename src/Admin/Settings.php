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

		</div>

		<?php

	}

}