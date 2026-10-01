<?php
/**
 * Site identity wiring.
 *
 * @package Peak
 */

namespace Peak\Site;

add_action(
	'init',
	function () {
		register_block_style( 'core/site-title', [ 'name' => 'domain', 'label' => __( 'Domain', 'peak' ) ] );
		register_block_pattern_category( 'peak', [ 'label' => __( 'Peak', 'peak' ) ] );
	}
);

// The wordmark shows the site's domain (deansas.org / photos.deansas.org).
add_filter(
	'render_block_core/site-title',
	function ( $html, $block ) {
		if ( ! str_contains( $block['attrs']['className'] ?? '', 'is-style-domain' ) ) {
			return $html;
		}
		return replace_link_text( $html, esc_html( host( home_url() ) ) );
	},
	10,
	2
);

function writing_url(): string {
	$page = (int) get_option( 'page_for_posts' );
	return $page ? (string) get_permalink( $page ) : home_url( '/' );
}

function photos_url(): string {
	$pages = get_posts(
		[
			'post_type'   => 'page',
			'post_status' => 'publish',
			'numberposts' => 1,
			'fields'      => 'ids',
			'meta_key'    => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => 'photos-grid', // phpcs:ignore WordPress.DB.SlowDBQuery
		]
	);
	return $pages ? (string) get_permalink( $pages[0] ) : (string) apply_filters( 'peak_photos_url', 'https://photos.deansas.org/' );
}
