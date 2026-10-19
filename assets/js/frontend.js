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
	let opener = null;

	document.querySelectorAll('.dmf-video-link').forEach((link) => {

		link.addEventListener('click', (event) => {

			event.preventDefault();

			const videoId = link.dataset.videoId;

			if (!videoId) {
				return;
			}

			opener = link;
			modal.hidden = false;
			modal.setAttribute('aria-hidden', 'false');
			document.body.classList.add('dmf-modal-open');

			openPlayer(videoId);
			closeButton.focus();

		});

	});

	function closeModal() {

		const returnTarget = opener;

		opener = null;

		player.innerHTML = '';

		modal.hidden = true;
		modal.setAttribute('aria-hidden', 'true');

		closeButton.hidden = false;

		document.body.classList.remove('dmf-modal-open');

		if (
			returnTarget
			&& document.contains(returnTarget)
			&& !returnTarget.hidden
			&& !returnTarget.hasAttribute('disabled')
		) {
			returnTarget.focus();
		}

	}

	closeButton.addEventListener('click', closeModal);

	overlay.addEventListener('click', closeModal);

	document.addEventListener('keydown', (event) => {

		if (event.key === 'Escape' && !modal.hidden) {
			closeModal();
			return;
		}

		if (event.key === 'Tab' && !modal.hidden) {
			trapFocus(event);
		}

	});

	function openPlayer(videoId) {

		const loading = document.createElement('div');

		loading.className = 'dmf-video-player__status';
		loading.setAttribute('role', 'status');
		loading.setAttribute('aria-live', 'polite');
		loading.setAttribute('aria-atomic', 'true');
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
		player.setAttribute('aria-busy', 'true');
		player.append(loading, iframe);

		iframe.addEventListener('load', () => {

			loading.remove();
			player.className = 'dmf-video-player dmf-video-player--ready';
			player.setAttribute('aria-busy', 'false');

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
		player.removeAttribute('aria-busy');

		const errorMessage = document.createElement('p');
		errorMessage.setAttribute('role', 'alert');
		errorMessage.textContent = 'No se ha podido cargar el vídeo.';

		player.append(
			errorMessage,
			closeErrorButton
		);

		closeButton.hidden = true;
		closeErrorButton.focus();

	}

	function trapFocus(event) {

		const focusableElements = Array.from(
			modal.querySelectorAll('button:not([hidden]), iframe, [href], [tabindex]:not([tabindex="-1"])')
		).filter((element) => {
			return !element.hasAttribute('disabled') && element.getAttribute('aria-hidden') !== 'true';
		});

		if (0 === focusableElements.length) {
			event.preventDefault();
			modal.focus();
			return;
		}

		const activeElement = document.activeElement;

		// Do not intercept Tab inside the cross-origin YouTube iframe.
		if (activeElement instanceof HTMLIFrameElement) {
			return;
		}

		const firstElement = focusableElements[0];
		const lastElement = focusableElements[focusableElements.length - 1];

		if (!modal.contains(activeElement)) {
			event.preventDefault();
			firstElement.focus();
			return;
		}

		if (event.shiftKey && activeElement === firstElement) {
			event.preventDefault();
			lastElement.focus();
			return;
		}

		if (!event.shiftKey && activeElement === lastElement) {
			event.preventDefault();
			firstElement.focus();
		}

	}

});
