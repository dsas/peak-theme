<?php
/**
 * Registers every block in blocks/ and the shared editor script.
 *
 * @package Peak
 */

namespace Peak\Blocks;

add_action(
	'init',
	function () {
		$folders = glob( get_theme_file_path( 'blocks/*/block.json' ) ) ?: [];

		wp_register_script(
			'peak-blocks-editor',
			get_theme_file_uri( 'blocks/editor.js' ),
			[ 'wp-blocks', 'wp-element', 'wp-server-side-render', 'wp-block-editor' ],
			(string) filemtime( get_theme_file_path( 'blocks/editor.js' ) ),
			true
		);
		$names = array_map( fn( $json ) => 'peak/' . basename( dirname( $json ) ), $folders );
		wp_add_inline_script( 'peak-blocks-editor', 'window.peakBlocks = ' . wp_json_encode( $names ) . ';', 'before' );

		foreach ( $folders as $json ) {
			register_block_type( dirname( $json ) );
		}
	}
);
