/**
 * DMF Media Frontend
 */

document.addEventListener('DOMContentLoaded', () => {

	const modal = document.getElementById('dmf-video-modal');

	if (!modal) {
		return;
	}

	const player = document.getElementById('dmf-video-player');
	const closeButton = modal.querySelector('.dmf-video-modal__close');
	const overlay = modal.querySelector('.dmf-video-modal__overlay');

	document.querySelectorAll('.dmf-video-link').forEach((link) => {

		link.addEventListener('click', (event) => {

			event.preventDefault();

			const videoId = link.dataset.videoId;

			if (!videoId) {
				return;
			}

			modal.hidden = false;
			document.body.classList.add('dmf-modal-open');

			openPlayer(videoId);

		});

	});

	function closeModal() {

		player.innerHTML = '';

		modal.hidden = true;

		closeButton.hidden = false;

		document.body.classList.remove('dmf-modal-open');

	}

	closeButton.addEventListener('click', closeModal);

	overlay.addEventListener('click', closeModal);

	document.addEventListener('keydown', (event) => {

		if (event.key === 'Escape' && !modal.hidden) {
			closeModal();
		}

	});

	function openPlayer(videoId) {

		const loading = document.createElement('div');

		loading.className = 'dmf-video-player__status';
		loading.innerHTML = `
			<span class="dmf-video-player__spinner" aria-hidden="true"></span>
			<span>Cargando vídeo…</span>
		`;

		const iframe = document.createElement('iframe');

		iframe.width = '100%';
		iframe.height = '100%';
		iframe.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1&playsinline=1&iv_load_policy=3`;
		iframe.title = 'YouTube video player';
		iframe.frameBorder = '0';
		iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
		iframe.allowFullscreen = true;

		player.innerHTML = '';
		player.className = 'dmf-video-player dmf-video-player--loading';
		player.append(loading, iframe);

		iframe.addEventListener('load', () => {

			loading.remove();
			player.className = 'dmf-video-player dmf-video-player--ready';

		});

		iframe.addEventListener('error', showPlayerError);

	}

	function showPlayerError() {

		const closeErrorButton = document.createElement('button');

		closeErrorButton.type = 'button';
		closeErrorButton.className = 'dmf-video-player__close-error';
		closeErrorButton.textContent = 'Cerrar';
		closeErrorButton.addEventListener('click', closeModal);

		player.innerHTML = '';
		player.className = 'dmf-video-player dmf-video-player--error';
		player.append(
			Object.assign(document.createElement('p'), {
				textContent: 'No se ha podido cargar el vídeo.',
			}),
			closeErrorButton
		);

		closeButton.hidden = true;

	}

});
