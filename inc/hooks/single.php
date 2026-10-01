<?php
/**
 * Writing post navigation skips photo posts.
 *
 * @package Peak
 */

namespace Peak\Single;

use Peak\Formats;

function photo_term_ids(): array {
	$ids = [];
	foreach ( Formats\PHOTO_FORMATS as $format ) {
		$term = get_term_by( 'slug', 'post-format-' . $format, 'post_format' );
		if ( $term ) {
			$ids[] = (int) $term->term_id;
		}
	}
	return $ids;
}

foreach ( [ 'previous', 'next' ] as $peak_adjacent ) {
	add_filter(
		"get_{$peak_adjacent}_post_excluded_terms",
		function ( $excluded ) {
			if ( ! is_singular( 'post' ) || Formats\is_photo_format( get_post_format() ) ) {
				return $excluded;
			}
			$ids = photo_term_ids();
			return $ids ? Formats\merge_excluded( $excluded, $ids ) : $excluded;
		}
	);
}
