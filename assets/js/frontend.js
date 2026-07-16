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

			player.innerHTML = `
				<iframe
					width="100%"
					height="100%"
					src="https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0"
					title="YouTube video player"
					frameborder="0"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					allowfullscreen>
				</iframe>
			`;

			modal.hidden = false;

			document.body.classList.add('dmf-modal-open');

		});

	});

	function closeModal() {

		player.innerHTML = '';

		modal.hidden = true;

		document.body.classList.remove('dmf-modal-open');

	}

	closeButton.addEventListener('click', closeModal);

	overlay.addEventListener('click', closeModal);

	document.addEventListener('keydown', (event) => {

		if (event.key === 'Escape' && !modal.hidden) {
			closeModal();
		}

	});

});