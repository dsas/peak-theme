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

/** term_taxonomy_ids of the photo post formats. */
function photo_term_taxonomy_ids(): array {
	$ids = [];
	foreach ( Formats\PHOTO_FORMATS as $format ) {
		$term = get_term_by( 'slug', 'post-format-' . $format, 'post_format' );
		if ( $term ) {
			$ids[] = (int) $term->term_taxonomy_id;
		}
	}
	return $ids;
}

function is_photo_single(): bool {
	return is_singular( 'post' ) && Formats\is_photo_format( get_post_format() );
}

// Photo post navigation only steps between photo posts.
foreach ( [ 'previous', 'next' ] as $peak_adjacent ) {
	add_filter(
		"get_{$peak_adjacent}_post_where",
		function ( $where ) {
			$ids = is_photo_single() ? photo_term_taxonomy_ids() : [];
			if ( ! $ids ) {
				return $where;
			}
			global $wpdb;
			return $where . " AND p.ID IN ( SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ( " . implode( ',', $ids ) . ' ) )';
		}
	);
}

// Photo post navigation shows a small thumbnail of the photo it links to.
add_filter(
	'render_block_core/post-navigation-link',
	function ( $html, $block ) {
		if ( '' === trim( $html ) || ! is_photo_single() || ! str_contains( $block['attrs']['className'] ?? '', 'peak-photo-nav__link' ) ) {
			return $html;
		}
		$previous = 'previous' === ( $block['attrs']['type'] ?? 'next' );
		$adjacent = get_adjacent_post( false, '', $previous );
		$thumb    = $adjacent ? get_the_post_thumbnail( $adjacent, 'thumbnail', [ 'alt' => '' ] ) : '';
		if ( ! $thumb ) {
			return $html;
		}
		// The thumbnail repeats the title link, so keep it out of the tab order and accessibility tree.
		$thumb_link = sprintf(
			'<a class="peak-photo-nav__thumb" href="%s" tabindex="-1" aria-hidden="true">%s</a>',
			esc_url( get_permalink( $adjacent ) ),
			$thumb
		);
		return preg_replace_callback(
			'/^(\s*<div\b[^>]*>)(.*)(<\/div>\s*)$/s',
			fn( $m ) => $m[1] . $thumb_link . '<span class="peak-photo-nav__text">' . $m[2] . '</span>' . $m[3],
			$html
		) ?? $html;
	},
	10,
	2
);

// Writing posts: mark portrait featured images so CSS can cap their height instead of
// showing them 68rem wide and well over a screen tall.
add_filter(
	'render_block_core/post-featured-image',
	function ( $html, $block ) {
		if ( '' === $html || ! str_contains( $block['attrs']['className'] ?? '', 'peak-single__image' ) ) {
			return $html;
		}
		$thumb = (int) get_post_thumbnail_id( get_queried_object_id() );
		$meta  = $thumb ? wp_get_attachment_metadata( $thumb ) : null;
		if ( 'portrait' !== \Peak\Aspect\orientation( \Peak\Aspect\ratio( $meta ?: null ) ) ) {
			return $html;
		}
		$tags = new \WP_HTML_Tag_Processor( $html );
		if ( $tags->next_tag() ) {
			$tags->add_class( 'is-portrait' );
		}
		return $tags->get_updated_html();
	},
	10,
	2
);
