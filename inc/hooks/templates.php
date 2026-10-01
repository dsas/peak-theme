<?php
/**
 * Template selection and photo thumbnails.
 *
 * @package Peak
 */

namespace Peak\Templates;

use Peak\Formats;

add_filter(
	'single_template_hierarchy',
	fn( $templates ) => Formats\single_hierarchy( $templates, get_post_format( get_queried_object_id() ) )
);

add_filter(
	'frontpage_template_hierarchy',
	function ( $templates ) {
		$front = (int) get_option( 'page_on_front' );
		return Formats\frontpage_hierarchy(
			$templates,
			(string) get_option( 'show_on_front' ),
			$front ? (string) get_page_template_slug( $front ) : ''
		);
	}
);

// Photo posts without a featured image use their first image (gallery or classic content).
add_filter(
	'post_thumbnail_id',
	function ( $thumbnail_id, $post ) {
		if ( ! $post || ! Formats\is_photo_format( get_post_format( $post ) ) ) {
			return $thumbnail_id;
		}
		if ( $thumbnail_id && wp_attachment_is_image( $thumbnail_id ) ) {
			return $thumbnail_id;
		}
		// A missing or non-image attachment counts as no featured image.
		$first = Formats\first_image_id( parse_blocks( get_post( $post )->post_content ) );
		return $first && wp_attachment_is_image( $first ) ? $first : 0;
	},
	10,
	2
);
