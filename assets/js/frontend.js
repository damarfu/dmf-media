/**
 * DMF Media Frontend
 *
 * El modal se imprime una sola vez desde wp_footer. Este script lo inicializa,
 * delega los clics de las tarjetas (también las que se añadan por AJAX) y
 * gestiona el reproductor, el foco y el bloqueo de scroll.
 */

(function () {
	'use strict';

	const MODAL_ID = 'dmf-video-modal';
	const LOAD_TIMEOUT = 8000;

	let modal = null;
	let player = null;
	let closeButton = null;
	let overlay = null;
	let opener = null;
	let loadTimer = null;
	let inerted = [];
	let previousPadding = '';

	document.addEventListener('click', onClick);
	document.addEventListener('keydown', onKeydown);

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

	function init() {
		const element = document.getElementById(MODAL_ID);

		if (!element || element === modal) {
			return;
		}

		modal = element;
		player = document.getElementById('dmf-video-player');
		closeButton = modal.querySelector('.dmf-video-modal__close');
		overlay = modal.querySelector('.dmf-video-modal__overlay');

		if (closeButton) {
			closeButton.addEventListener('click', closeModal);
		}

		if (overlay) {
			overlay.addEventListener('click', closeModal);
		}
	}

	function onClick(event) {
		const target = event.target;

		if (!(target instanceof Element)) {
			return;
		}

		const link = target.closest('.dmf-video-link');

		if (!link) {
			return;
		}

		// Respetar la navegación normal: nueva pestaña, botón central, etc.
		if (event.defaultPrevented || event.button !== 0) {
			return;
		}

		if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
			return;
		}

		init();

		// Sin modal o sin reproductor dejamos que el enlace funcione.
		if (!modal || !player || !closeButton) {
			return;
		}

		const videoId = link.getAttribute('data-video-id');

		if (!videoId) {
			return;
		}

		event.preventDefault();
		openModal(link, videoId);
	}

	function onKeydown(event) {
		if (!modal || modal.hidden) {
			return;
		}

		if (event.key === 'Escape') {
			event.preventDefault();
			closeModal();
			return;
		}

		if (event.key === 'Tab') {
			trapFocus(event);
		}
	}

	function openModal(link, videoId) {
		opener = link;
		modal.hidden = false;
		modal.setAttribute('aria-hidden', 'false');
		lockScroll();
		setBackgroundInert(true);
		closeButton.hidden = false;
		openPlayer(videoId);
		closeButton.focus();
	}

	function closeModal() {
		if (!modal || modal.hidden) {
			return;
		}

		clearLoadTimer();
		stopVideo();

		modal.hidden = true;
		modal.setAttribute('aria-hidden', 'true');

		unlockScroll();
		setBackgroundInert(false);

		if (closeButton) {
			closeButton.hidden = false;
		}

		const target = opener;

		opener = null;

		if (target && document.contains(target) && !target.hasAttribute('disabled')) {
			target.focus();
		}
	}

	function openPlayer(videoId) {
		clearLoadTimer();

		if (!player) {
			return;
		}

		const loading = document.createElement('div');
		loading.className = 'dmf-video-player__status';
		loading.setAttribute('role', 'status');
		loading.setAttribute('aria-live', 'polite');
		loading.setAttribute('aria-atomic', 'true');

		const spinner = document.createElement('span');
		spinner.className = 'dmf-video-player__spinner';
		spinner.setAttribute('aria-hidden', 'true');

		const label = document.createElement('span');
		label.textContent = 'Cargando vídeo…';

		loading.append(spinner, label);

		const iframe = document.createElement('iframe');
		iframe.width = '100%';
		iframe.height = '100%';
		iframe.src = 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1&rel=0&modestbranding=1&playsinline=1&iv_load_policy=3';
		iframe.title = 'Reproductor de vídeo de YouTube';
		iframe.setAttribute('frameborder', '0');
		iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
		iframe.setAttribute('allowfullscreen', '');
		iframe.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');

		player.innerHTML = '';
		player.className = 'dmf-video-player dmf-video-player--loading';
		player.setAttribute('aria-busy', 'true');
		player.append(loading, iframe);

		iframe.addEventListener('load', function () {
			if (!player || !iframe.parentNode) {
				return;
			}

			clearLoadTimer();

			if (loading.parentNode) {
				loading.remove();
			}

			player.className = 'dmf-video-player dmf-video-player--ready';
			player.setAttribute('aria-busy', 'false');
		});

		// Si el iframe no carga (bloqueado, red caída), mostramos el error.
		loadTimer = window.setTimeout(function () {
			if (!player || !iframe.parentNode) {
				return;
			}

			showPlayerError();
		}, LOAD_TIMEOUT);
	}

	function showPlayerError() {
		clearLoadTimer();

		if (!player) {
			return;
		}

		const message = document.createElement('p');
		message.setAttribute('role', 'alert');
		message.textContent = 'No se ha podido cargar el vídeo.';

		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'dmf-video-player__close-error';
		button.textContent = 'Cerrar';
		button.addEventListener('click', closeModal);

		player.innerHTML = '';
		player.className = 'dmf-video-player dmf-video-player--error';
		player.removeAttribute('aria-busy');
		player.append(message, button);

		if (closeButton) {
			closeButton.hidden = true;
		}

		button.focus();
	}

	function stopVideo() {
		if (!player) {
			return;
		}

		player.innerHTML = '';
		player.className = 'dmf-video-player';
		player.removeAttribute('aria-busy');
	}

	function clearLoadTimer() {
		if (loadTimer) {
			window.clearTimeout(loadTimer);
			loadTimer = null;
		}
	}

	function trapFocus(event) {
		const focusable = getFocusable();

		if (!focusable.length) {
			event.preventDefault();
			modal.focus();
			return;
		}

		const active = document.activeElement;

		// No interceptar Tab dentro del iframe de YouTube (otro origen).
		if (active instanceof HTMLIFrameElement) {
			return;
		}

		const first = focusable[0];
		const last = focusable[focusable.length - 1];

		if (!modal.contains(active)) {
			event.preventDefault();
			first.focus();
			return;
		}

		if (event.shiftKey && active === first) {
			event.preventDefault();
			last.focus();
			return;
		}

		if (!event.shiftKey && active === last) {
			event.preventDefault();
			first.focus();
		}
	}

	function getFocusable() {
		return Array.prototype.filter.call(
			modal.querySelectorAll('button:not([hidden]), iframe, a[href], [tabindex]:not([tabindex="-1"])'),
			function (element) {
				return !element.hasAttribute('disabled') && !element.hasAttribute('hidden');
			}
		);
	}

	function lockScroll() {
		const gap = window.innerWidth - document.documentElement.clientWidth;

		previousPadding = document.body.style.paddingRight;

		if (gap > 0) {
			document.body.style.paddingRight = gap + 'px';
		}

		document.body.classList.add('dmf-modal-open');
	}

	function unlockScroll() {
		document.body.classList.remove('dmf-modal-open');
		document.body.style.paddingRight = previousPadding;
		previousPadding = '';
	}

	function setBackgroundInert(enabled) {
		if (enabled) {
			inerted = [];

			Array.prototype.forEach.call(document.body.children, function (element) {
				if (element === modal) {
					return;
				}

				inerted.push({
					element: element,
					inert: element.hasAttribute('inert'),
					ariaHidden: element.getAttribute('aria-hidden')
				});

				element.setAttribute('inert', '');
				element.setAttribute('aria-hidden', 'true');
			});

			return;
		}

		inerted.forEach(function (state) {
			if (!state.inert) {
				state.element.removeAttribute('inert');
			}

			if (state.ariaHidden === null) {
				state.element.removeAttribute('aria-hidden');
			} else {
				state.element.setAttribute('aria-hidden', state.ariaHidden);
			}
		});

		inerted = [];
	}
}());
