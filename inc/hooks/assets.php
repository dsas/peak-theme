<?php
/**
 * Stylesheet loading.
 *
 * @package Peak
 */

namespace Peak\Assets;

/** @return string[] Absolute paths of every theme stylesheet, alphabetical. */
function stylesheets(): array {
	return glob( get_theme_file_path( 'assets/css/*.css' ) ) ?: [];
}

add_action(
	'wp_enqueue_scripts',
	function () {
		foreach ( stylesheets() as $path ) {
			$name = basename( $path, '.css' );
			wp_enqueue_style( 'peak-' . $name, get_theme_file_uri( 'assets/css/' . $name . '.css' ), [], (string) filemtime( $path ) );
		}
	}
);

add_action(
	'after_setup_theme',
	function () {
		add_editor_style( array_map( fn( $path ) => 'assets/css/' . basename( $path ), stylesheets() ) );
	}
);

add_action(
	'wp_head',
	function () {
		foreach ( [
			'newsreader-latin-wght-normal.woff2',
			'young-serif-latin-400-normal.woff2',
			'inter-latin-wght-normal.woff2',
			'jetbrains-mono-latin-400-normal.woff2',
		] as $file ) {
			printf(
				'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
				esc_url( get_theme_file_uri( 'assets/fonts/' . $file ) )
			);
		}
	},
	1
);
