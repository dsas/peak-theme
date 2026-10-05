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
			[ 'wp-blocks', 'wp-element', 'wp-server-side-render', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-hooks' ],
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

// Version each Peak block's assets (e.g. the style switcher's view.js) by its files' newest
// modification time rather than the fixed "version" in block.json. The files are served with
// long cache lifetimes, so a fixed ?ver= would keep returning visitors on the old script.
add_filter(
	'block_type_metadata',
	function ( $metadata ) {
		if ( ! str_starts_with( $metadata['name'] ?? '', 'peak/' ) || empty( $metadata['file'] ) ) {
			return $metadata;
		}
		$files = glob( dirname( $metadata['file'] ) . '/*' ) ?: [];
		$mtime = max( array_map( 'filemtime', $files ) ?: [ 0 ] );
		if ( $mtime ) {
			$metadata['version'] = (string) $mtime;
		}
		return $metadata;
	}
);

// The year rail and style switcher only make sense in templates: keep them out of the
// inserter when writing posts and pages (the Site Editor still offers them). Done in the
// editor script, so blocks that other plugins register only in JavaScript aren't affected.
add_action(
	'enqueue_block_editor_assets',
	function () {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'post' === $screen->base ) {
			wp_add_inline_script( 'peak-blocks-editor', 'window.peakTemplateOnlyBlocks = ' . wp_json_encode( [ 'peak/year-rail', 'peak/style-switcher' ] ) . ';', 'before' );
		}
	}
);
