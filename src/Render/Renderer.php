<?php

declare(strict_types=1);

namespace DMF\Media\Render;

defined( 'ABSPATH' ) || exit;

final class Renderer {

	public function render(): string {

		ob_start();
		?>

		<div class="dmf-media">

			<h3>DMF Media funciona 🚀</h3>

			<p>La nueva arquitectura está correctamente cargada.</p>

		</div>

		<?php

		return (string) ob_get_clean();

	}

}