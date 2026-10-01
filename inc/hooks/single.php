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

/** Untitled and not password-protected (a protected post's words must not leak into titles or links). */
function is_untitled( \WP_Post $post ): bool {
	return '' === trim( $post->post_title ) && '' === $post->post_password;
}

/** Stand-in title for an untitled post (old asides): its first few words, as on the timeline. */
function untitled_label( \WP_Post $post ): string {
	return \Peak\Timeline\display_title( '', wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) );
}

// Untitled posts: give the page a heading (for screen readers and the document outline) and a
// browser-tab title, instead of nothing and the bare site name.
add_filter(
	'render_block_core/post-title',
	function ( $html, $block, $instance ) {
		if ( '' !== trim( $html ) || 1 !== (int) ( $block['attrs']['level'] ?? 2 ) || ! is_singular() ) {
			return $html;
		}
		$post_id = (int) ( $instance->context['postId'] ?? 0 );
		$post    = $post_id === get_queried_object_id() ? get_post( $post_id ) : null;
		if ( ! $post || ! is_untitled( $post ) ) {
			return $html;
		}
		return '<h1 class="wp-block-post-title screen-reader-text">' . esc_html( untitled_label( $post ) ) . '</h1>';
	},
	10,
	3
);

add_filter(
	'document_title_parts',
	function ( $parts ) {
		$post = is_singular() ? get_queried_object() : null;
		if ( $post instanceof \WP_Post && is_untitled( $post ) ) {
			$parts['title'] = untitled_label( $post );
		}
		return $parts;
	}
);

// Older/newer links to an untitled post: its first few words instead of core's "Previous Post".
foreach ( [ 'previous', 'next' ] as $peak_adjacent ) {
	add_filter(
		"{$peak_adjacent}_post_link",
		function ( $output, $format, $link, $post, $adjacent ) {
			if ( ! $post instanceof \WP_Post || ! is_untitled( $post ) ) {
				return $output;
			}
			$fallback = 'previous' === $adjacent ? __( 'Previous Post' ) : __( 'Next Post' ); // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- core's own string.
			return str_replace( '>' . $fallback . '<', '>' . esc_html( untitled_label( $post ) ) . '<', $output );
		},
		10,
		5
	);
}
