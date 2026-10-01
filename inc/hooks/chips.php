<?php
/**
 * Topic chips on writing archives.
 *
 * @package Peak
 */

namespace Peak\Chips;

use Peak\Site;

add_action(
	'init',
	fn() => register_block_style( 'core/categories', [ 'name' => 'chips', 'label' => __( 'Chips', 'peak' ) ] )
);

add_filter(
	'render_block_core/categories',
	function ( $html, $block ) {
		if ( ! str_contains( $block['attrs']['className'] ?? '', 'is-style-chips' ) ) {
			return $html;
		}
		return prepend_all( $html, esc_url( Site\writing_url() ), is_home(), esc_html__( 'All', 'peak' ) );
	},
	10,
	2
);
