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

// Archives with photos but no writing (photos site) use the photo grid instead of the empty timeline.
foreach ( [ 'archive', 'date', 'tag', 'category', 'taxonomy' ] as $peak_archive ) {
	add_filter(
		"{$peak_archive}_template_hierarchy",
		fn( $templates ) => Formats\archive_hierarchy(
			$templates,
			have_posts(),
			[] !== \Peak\Timeline\items( \Peak\Timeline\current_context() )
		)
	);
}

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

// Names and descriptions for the theme's own templates in the Site Editor, which otherwise
// shows bare file names such as "single-photo". Not for Photos grid: listing a template here
// makes it a standard template type, and pages can only pick custom ones (theme.json names it).
add_filter(
	'default_template_types',
	function ( $types ) {
		$types['single-photo']   = [
			'title'       => __( 'Photo post', 'peak' ),
			'description' => __( 'Displays a post with the Image or Gallery format: the photo full width, then any gallery in justified rows.', 'peak' ),
		];
		$types['photos-archive'] = [
			'title'       => __( 'Photo archive', 'peak' ),
			'description' => __( 'Displays a tag, category or date archive that has only photo posts, as a justified photo grid.', 'peak' ),
		];
		$types['page-now']       = [
			'title'       => __( 'Page: Now', 'peak' ),
			'description' => __( 'The Now page: the page content with an "Updated" date under the title.', 'peak' ),
		];
		return $types;
	}
);
