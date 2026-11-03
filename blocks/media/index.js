/**
 * DMF Media - bloque de Gutenberg (sin build)
 *
 * Usa las APIs globales de WordPress (wp.blocks, wp.element, wp.components,
 * wp.blockEditor y wp.serverSideRender). Los atributos y los metadatos se
 * reciben desde PHP con wp_localize_script, para que block.json siga siendo
 * la única fuente de verdad.
 */

(function (blocks, element, blockEditor, components, serverSideRender) {
	'use strict';

	const el = element.createElement;
	const Fragment = element.Fragment;
	const InspectorControls = blockEditor.InspectorControls;
	const useBlockProps = blockEditor.useBlockProps;
	const PanelBody = components.PanelBody;
	const SelectControl = components.SelectControl;
	const TextControl = components.TextControl;
	const RangeControl = components.RangeControl;
	const Notice = components.Notice;
	const ServerSideRender = serverSideRender && serverSideRender.default ? serverSideRender.default : serverSideRender;
	const settings = window.dmfMediaBlock;

	if (!settings || !settings.name || !blocks || !ServerSideRender) {
		return;
	}

	blocks.registerBlockType(settings.name, {
		title: settings.title,
		description: settings.description,
		category: settings.category,
		icon: settings.icon,
		keywords: settings.keywords,
		supports: settings.supports,
		attributes: settings.attributes,
		edit: Edit,
		save: function () {
			return null;
		}
	});

	function Edit(props) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps = useBlockProps({ className: 'dmf-media-block' });

		const source = attributes.source || 'default';
		const isVideo = source === 'video';
		const isPlaylist = source === 'playlist';
		const isChannel = source === 'channel';
		const hasDefaultChannel = settings.hasDefaultChannel === true || settings.hasDefaultChannel === '1';
		const missingDefaultChannel = source === 'default' && !hasDefaultChannel;

		function update(key, value) {
			const next = {};
			next[key] = value;
			setAttributes(next);
		}

		return el(
			Fragment,
			null,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: 'Fuente', initialOpen: true },
					el(SelectControl, {
						label: 'Origen de los vídeos',
						value: source,
						options: [
							{ label: 'Canal configurado en Ajustes', value: 'default' },
							{ label: 'Otro canal', value: 'channel' },
							{ label: 'Playlist pública', value: 'playlist' },
							{ label: 'Vídeo concreto', value: 'video' }
						],
						onChange: function (value) {
							update('source', value);
						}
					}),
					missingDefaultChannel
						? el(
							Notice,
							{ status: 'warning', isDismissible: false },
							'No hay ningún canal configurado en Ajustes → DMF Media. Configúralo o elige otro origen.'
						)
						: null,
					isChannel
						? el(TextControl, {
							label: 'Channel ID o URL',
							value: attributes.channel || '',
							placeholder: 'UCxxxxxxxxxxxxxxxx',
							help: 'Empieza por UC o pega una URL /channel/UC...',
							onChange: function (value) {
								update('channel', value);
							}
						})
						: null,
					isPlaylist
						? el(TextControl, {
							label: 'Playlist ID o URL',
							value: attributes.playlist || '',
							placeholder: 'PLxxxxxxxx',
							help: 'Acepta el ID o una URL de playlist.',
							onChange: function (value) {
								update('playlist', value);
							}
						})
						: null,
					isVideo
						? el(TextControl, {
							label: 'ID o URL del vídeo',
							value: attributes.video || '',
							placeholder: 'https://www.youtube.com/watch?v=...',
							help: 'Acepta watch, youtu.be, shorts, embed y live.',
							onChange: function (value) {
								update('video', value);
							}
						})
						: null
				),
				isVideo
					? null
					: el(
						PanelBody,
						{ title: 'Presentación', initialOpen: true },
						el(RangeControl, {
							label: 'Número de vídeos',
							value: Number(attributes.limit) || 0,
							min: 0,
							max: 15,
							help: '0 usa el límite configurado en Ajustes → DMF Media.',
							onChange: function (value) {
								update('limit', Number(value) || 0);
							}
						}),
						el(RangeControl, {
							label: 'Omitir los más recientes',
							value: Number(attributes.offset) || 0,
							min: 0,
							max: 15,
							help: 'Vídeos recientes que no se muestran. Solo canales y playlists.',
							onChange: function (value) {
								update('offset', Number(value) || 0);
							}
						})
					)
			),
			el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'dmf-media-block__preview' },
					el(ServerSideRender, {
						block: settings.name,
						attributes: attributes,
						LoadingResponsePlaceholder: function () {
							return el('p', { className: 'dmf-media-block__loading' }, 'Cargando vista previa…');
						}
					})
				)
			)
		);
	}
}(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.serverSideRender));
