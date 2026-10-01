<?php
/**
 * Justified rows for the Photos grid and gallery posts.
 *
 * @package Peak
 */

namespace Peak\Justified;

use Peak\Aspect;

add_action(
	'init',
	function () {
		register_block_style( 'core/post-template', [ 'name' => 'justified', 'label' => __( 'Justified', 'peak' ) ] );
		register_block_style( 'core/gallery', [ 'name' => 'justified', 'label' => __( 'Justified', 'peak' ) ] );
	}
);

// Each grid item gets its featured image's aspect ratio; gallery posts get a stack marker.
add_filter(
	'render_block_core/post-template',
	function ( $html, $block ) {
		if ( ! str_contains( $block['attrs']['className'] ?? '', 'is-style-justified' ) ) {
			return $html;
		}
		$tags = new \WP_HTML_Tag_Processor( $html );
		while ( $tags->next_tag( 'li' ) ) {
			$post_id = Aspect\post_id_from_class( (string) $tags->get_attribute( 'class' ) );
			if ( ! $post_id ) {
				continue;
			}
			$thumb = (int) get_post_thumbnail_id( $post_id );
			$tags->set_attribute( 'style', Aspect\style_value( Aspect\ratio( $thumb ? ( wp_get_attachment_metadata( $thumb ) ?: null ) : null ) ) );
			if ( 'gallery' === get_post_format( $post_id ) ) {
				$tags->add_class( 'is-gallery' );
			}
		}
		return $tags->get_updated_html();
	},
	10,
	2
);

// Every image block carries its ratio so justified galleries can lay out rows.
add_filter(
	'render_block_core/image',
	function ( $html, $block ) {
		$id = (int) ( $block['attrs']['id'] ?? 0 );
		if ( ! $id || '' === $html ) {
			return $html;
		}
		$tags = new \WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag() ) {
			$tags->set_attribute( 'style', Aspect\style_value( Aspect\ratio( wp_get_attachment_metadata( $id ) ?: null ) ) . (string) $tags->get_attribute( 'style' ) );
		}
		return $tags->get_updated_html();
	},
	10,
	2
);
