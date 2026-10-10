/**
 * Gutenberg Block Editor Script for Dynamic Date & Time
 */
(function (wp) {
	'use strict';

	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls } = wp.blockEditor || wp.editor;
	const { PanelBody, SelectControl, TextControl, ToggleControl } = wp.components;
	const ServerSideRender = wp.serverSideRender || null;
	const { __ } = wp.i18n;

	const blockData = window.wpcalBlockData || { presets: [], types: [] };

	registerBlockType('wpcalibrate/dynamic-date-time', {
		title: __('Dynamic Date & Time', 'wpcalibrate-dynamic-date-time'),
		icon: 'calendar-alt',
		category: 'text',

		edit: function (props) {
			const { attributes, setAttributes } = props;

			const inspector = el(
				InspectorControls,
				{},
				el(
					PanelBody,
					{ title: __('Date & Time Settings', 'wpcalibrate-dynamic-date-time'), initialOpen: true },

					// Preset selector
					el(SelectControl, {
						label: __('Preset (Optional)', 'wpcalibrate-dynamic-date-time'),
						value: attributes.preset,
						options: blockData.presets,
						help: __('Select an administrator-defined preset to automatically populate formatting and rules.', 'wpcalibrate-dynamic-date-time'),
						onChange: function (val) {
							setAttributes({ preset: val });
						},
					}),

					// Output Type
					el(SelectControl, {
						label: __('Output Type', 'wpcalibrate-dynamic-date-time'),
						value: attributes.type,
						options: blockData.types,
						onChange: function (val) {
							setAttributes({ type: val });
						},
					}),

					// Format string
					el(TextControl, {
						label: __('Date / Time Format', 'wpcalibrate-dynamic-date-time'),
						value: attributes.format,
						help: __('PHP date tokens (e.g. F j, Y or g:i A). Leave empty to use global default.', 'wpcalibrate-dynamic-date-time'),
						onChange: function (val) {
							setAttributes({ format: val });
						},
					}),

					// Timezone Mode
					el(SelectControl, {
						label: __('Timezone Mode', 'wpcalibrate-dynamic-date-time'),
						value: attributes.timezone,
						options: [
							{ value: 'site', label: __('WordPress Site Timezone', 'wpcalibrate-dynamic-date-time') },
							{ value: 'visitor', label: __('Visitor Browser Timezone', 'wpcalibrate-dynamic-date-time') },
						],
						onChange: function (val) {
							setAttributes({ timezone: val });
						},
					}),

					// Offset
					el(TextControl, {
						label: __('Relative Day Offset', 'wpcalibrate-dynamic-date-time'),
						value: attributes.offset,
						placeholder: '+1 day',
						help: __('e.g. +1 day, +7 days, -1 day, 0 days.', 'wpcalibrate-dynamic-date-time'),
						onChange: function (val) {
							setAttributes({ offset: val });
						},
					}),

					// Live update toggle
					el(ToggleControl, {
						label: __('Live Updating Clock', 'wpcalibrate-dynamic-date-time'),
						checked: !!attributes.live,
						help: __('Update every second without page reloads (ideal for live clocks).', 'wpcalibrate-dynamic-date-time'),
						onChange: function (val) {
							setAttributes({ live: val });
						},
					})
				)
			);

			// Preview in editor
			let previewContent;
			if (ServerSideRender) {
				previewContent = el(ServerSideRender, {
					block: 'wpcalibrate/dynamic-date-time',
					attributes: attributes,
				});
			} else {
				previewContent = el(
					'div',
					{ className: 'wpcal-editor-placeholder' },
					el('span', { className: 'dashicons dashicons-calendar-alt' }),
					el('strong', {}, __('Dynamic Date & Time: ', 'wpcalibrate-dynamic-date-time')),
					attributes.type + (attributes.offset ? ' (' + attributes.offset + ')' : '')
				);
			}

			return el(
				Fragment,
				{},
				inspector,
				el(
					'div',
					{ className: 'wpcal-block-wrapper ' + (attributes.className || '') },
					previewContent
				)
			);
		},

		save: function () {
			return null; // Dynamic block rendered on server
		},
	});
})(window.wp);
