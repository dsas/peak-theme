/* Shared editor UI for Peak's server-rendered blocks: a live server preview. */
( function ( wp, names ) {
	const { registerBlockType } = wp.blocks;
	const { createElement: el } = wp.element;
	const { useBlockProps } = wp.blockEditor;
	const ServerSideRender = wp.serverSideRender;

	( names || [] ).forEach( ( name ) => {
		registerBlockType( name, {
			edit: ( props ) =>
				el( 'div', useBlockProps(), el( ServerSideRender, { block: name, attributes: props.attributes } ) ),
			save: () => null,
		} );
	} );
} )( window.wp, window.peakBlocks );
