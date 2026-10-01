<?php
/**
 * Photo post behaviour.
 *
 * @package Peak
 */

namespace Peak\Photo;

use Peak\Aspect;
use Peak\Formats;

/** Attachment ID shown as the hero on the current photo single, else 0. */
function hero_id(): int {
	static $id = null;
	if ( null !== $id ) {
		return $id;
	}
	if ( ! is_singular( 'post' ) ) {
		return 0; // Not cached: the query may not be ready yet.
	}
	$post_id = get_queried_object_id();
	$id      = Formats\is_photo_format( get_post_format( $post_id ) ) ? (int) get_post_thumbnail_id( $post_id ) : 0;
	return $id;
}

// Don't show the hero image twice: drop it from the post content.
add_filter(
	'render_block_core/image',
	function ( $html, $block ) {
		$id = (int) ( $block['attrs']['id'] ?? 0 );
		return ( $id && $id === hero_id() ) ? '' : $html;
	},
	5,
	2
);

// Same for classic (Keyring/Flickr import) content: drop the <img> that is the hero file.
add_filter(
	'render_block',
	function ( $html, $block ) {
		if ( null !== ( $block['blockName'] ?? null ) || ! hero_id() ) {
			return $html;
		}
		$file = get_attached_file( hero_id() );
		if ( ! $file ) {
			return $html;
		}
		$stem = preg_replace( '/-scaled$/', '', pathinfo( $file, PATHINFO_FILENAME ) );
		return strip_classic_image( $html, (string) $stem );
	},
	10,
	2
);

// Portrait heroes are shown at screen height instead of full width.
add_filter(
	'render_block_core/post-featured-image',
	function ( $html, $block ) {
		if ( '' === $html || ! hero_id() || ! str_contains( $block['attrs']['className'] ?? '', 'peak-photo__image' ) ) {
			return $html;
		}
		$ratio = Aspect\ratio( wp_get_attachment_metadata( hero_id() ) ?: null );
		$tags  = new \WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag() ) {
			$tags->add_class( 'is-' . Aspect\orientation( $ratio ) );
		}
		if ( $tags->next_tag( 'img' ) ) {
			if ( $ratio < 1 ) {
				$tags->set_attribute( 'sizes', Aspect\portrait_hero_sizes( $ratio ) );
			}
			if ( '' === trim( (string) $tags->get_attribute( 'alt' ) ) ) {
				$tags->set_attribute( 'alt', get_the_title( get_queried_object_id() ) );
			}
		}
		return $tags->get_updated_html();
	},
	10,
	2
);
