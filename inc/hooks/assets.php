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

// The cyclist on the homepage hills.
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! is_front_page() ) {
			return;
		}
		$path = get_theme_file_path( 'assets/js/rider.js' );
		wp_enqueue_script( 'peak-rider', get_theme_file_uri( 'assets/js/rider.js' ), [], (string) filemtime( $path ), [ 'strategy' => 'defer', 'in_footer' => true ] );
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

// Use the browser's own emoji rather than WordPress's Twemoji replacement script: it adds
// JavaScript to every page and splits some sequences (e.g. ✍🏻 renders as ✍ plus a swatch).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
