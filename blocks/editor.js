/* Shared editor UI for Peak's server-rendered blocks: a live server preview, plus settings toggles where a block has them. */
( function ( wp, names ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { useBlockProps, InspectorControls } = wp.blockEditor;
	const { PanelBody, ToggleControl } = wp.components;
	const { __ } = wp.i18n;
	const ServerSideRender = wp.serverSideRender;

	// Boolean attributes shown as toggles in the block sidebar, per block.
	const toggles = {
		'peak/timeline': [
			{ attribute: 'showThumbnails', label: __( 'Show thumbnails', 'peak' ), help: __( 'For posts with a featured image. Timeline layout only.', 'peak' ) },
			{ attribute: 'showExcerpt', label: __( 'Show excerpts', 'peak' ), help: __( 'Only hand-written excerpts are shown. Timeline layout only.', 'peak' ) },
		],
	};

	( names || [] ).forEach( ( name ) => {
		registerBlockType( name, {
			edit: ( props ) => {
				const preview = el( 'div', useBlockProps(), el( ServerSideRender, { block: name, attributes: props.attributes } ) );
				if ( ! toggles[ name ] ) {
					return preview;
				}
				const controls = el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Settings', 'peak' ) },
						toggles[ name ].map( ( t ) =>
							el( ToggleControl, {
								key: t.attribute,
								label: t.label,
								help: t.help,
								checked: !! props.attributes[ t.attribute ],
								onChange: ( value ) => props.setAttributes( { [ t.attribute ]: value } ),
								__nextHasNoMarginBottom: true,
							} )
						)
					)
				);
				return el( Fragment, null, controls, preview );
			},
			save: () => null,
		} );
	} );
} )( window.wp, window.peakBlocks );
