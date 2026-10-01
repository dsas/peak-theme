<?php
/**
 * Content block styles.
 *
 * @package Peak
 */

namespace Peak\Content;

add_action(
	'init',
	function () {
		register_block_style( 'core/group', [ 'name' => 'boxout', 'label' => __( 'Boxout', 'peak' ) ] );
		register_block_style( 'core/list', [ 'name' => 'timeline', 'label' => __( 'Timeline', 'peak' ) ] );
		register_block_style( 'core/list', [ 'name' => 'emoji', 'label' => __( 'Emoji list', 'peak' ) ] );
	}
);

// The Now page's "Updated" date: core hides a modified date that isn't later than the
// publish date, so fall back to the publish date rather than showing "Updated" alone.
add_filter(
	'render_block_core/post-date',
	function ( $html, $block, $instance ) {
		$attrs = $block['attrs'] ?? [];
		if ( '' !== trim( $html ) || 'modified' !== ( $attrs['displayType'] ?? '' ) || ! str_contains( $attrs['className'] ?? '', 'peak-updated__date' ) ) {
			return $html;
		}
		$block['attrs']['displayType'] = 'date';
		// Keep the post context (postId) the original block had.
		return ( new \WP_Block( $block, $instance->context ?? [] ) )->render();
	},
	10,
	3
);

// Emoji lists: mark each item's leading emoji so CSS can hang it in the margin.
add_filter(
	'render_block_core/list',
	function ( $html, $block ) {
		if ( ! str_contains( $block['attrs']['className'] ?? '', 'is-style-emoji' ) ) {
			return $html;
		}
		return wrap_leading_emoji( $html );
	},
	10,
	2
);

// Category and tag archives: a small "Category" / "Tag" label above the archive heading,
// so a bare term name such as "reading" says what it is.
add_filter(
	'render_block_core/query-title',
	function ( $html ) {
		if ( is_category() ) {
			$label = __( 'Category', 'peak' );
		} elseif ( is_tag() ) {
			$label = __( 'Tag', 'peak' );
		} else {
			return $html;
		}
		return '<p class="peak-archive-label">' . esc_html( $label ) . '</p>' . $html;
	}
);

// Comments written with WordPress.com's block-based comment form store block delimiters;
// strip them before wpautop (priority 30) so they don't become stray line breaks.
add_filter(
	'comment_text',
	fn( $text ) => is_string( $text ) ? strip_block_delimiters( $text ) : $text,
	5
);

// Day archives: "2 January 2022", matching the date style used across the theme, rather than
// the site's date format setting.
add_filter(
	'get_the_archive_title',
	fn( $title ) => is_day() ? get_the_date( 'j F Y' ) : $title
);
