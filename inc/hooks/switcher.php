<?php
/**
 * Outputs colourway CSS and applies the visitor's stored choice before first paint.
 *
 * @package Peak
 */

namespace Peak\Variations;

function load(): array {
	static $list = null;
	if ( null !== $list ) {
		return $list;
	}
	$base  = wp_json_file_decode( get_theme_file_path( 'theme.json' ), [ 'associative' => true ] ) ?: [];
	$files = [];
	foreach ( glob( get_theme_file_path( 'styles/*.json' ) ) ?: [] as $path ) {
		$files[ basename( $path, '.json' ) ] = wp_json_file_decode( $path, [ 'associative' => true ] ) ?: [];
	}
	return $list = collect( $base, $files );
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$list = load();
		wp_register_style( 'peak-variations', false, [], \Peak\VERSION );
		wp_enqueue_style( 'peak-variations' );
		wp_add_inline_style( 'peak-variations', to_css( $list ) . prefers_dark_css( $list ) );
	},
	20
);

add_action(
	'wp_head',
	function () {
		$slugs = wp_json_encode( array_column( load(), 'slug' ) );
		wp_print_inline_script_tag(
			"(function(){try{var s=localStorage.getItem('peak-style');if(s&&{$slugs}.indexOf(s)>-1){document.documentElement.setAttribute('data-style',s);}}catch(e){}})();"
		);
	},
	0
);
