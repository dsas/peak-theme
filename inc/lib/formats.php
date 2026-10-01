<?php
/**
 * Photo post formats and template selection. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Formats;

const PHOTO_FORMATS = [ 'image', 'gallery' ];

function is_photo_format( $format ): bool {
	return is_string( $format ) && in_array( $format, PHOTO_FORMATS, true );
}

/** Image and Gallery posts share the `single-photo` template. */
function single_hierarchy( array $templates, $format ): array {
	if ( ! is_photo_format( $format ) ) {
		return $templates;
	}
	return array_values( array_unique( array_merge( [ 'single-photo.php' ], $templates ) ) );
}

/**
 * Skip front-page.html when it would be wrong: the front page lists latest posts
 * (fall through to home.html), or the static front page has its own template
 * (e.g. Photos grid on photos.deansas.org).
 */
function frontpage_hierarchy( array $templates, string $show_on_front, string $assigned_template ): array {
	if ( 'page' !== $show_on_front ) {
		return [];
	}
	if ( '' !== $assigned_template && 'default' !== $assigned_template ) {
		return [];
	}
	return $templates;
}

/** First attachment ID in parsed blocks, including classic (freeform) content. */
function first_image_id( array $blocks ): int {
	foreach ( $blocks as $block ) {
		$name  = $block['blockName'] ?? null;
		$attrs = $block['attrs'] ?? [];

		if ( 'core/image' === $name && ! empty( $attrs['id'] ) ) {
			return (int) $attrs['id'];
		}
		if ( 'core/gallery' === $name && ! empty( $attrs['ids'] ) ) {
			return (int) reset( $attrs['ids'] );
		}
		if ( null === $name ) {
			$id = first_image_id_in_html( (string) ( $block['innerHTML'] ?? '' ) );
			if ( $id ) {
				return $id;
			}
		}

		$id = first_image_id( $block['innerBlocks'] ?? [] );
		if ( $id ) {
			return $id;
		}
	}
	return 0;
}

function first_image_id_in_html( string $html ): int {
	if ( preg_match( '/\[gallery\b[^\]]*\bids=["\']?(\d+)/', $html, $m ) ) {
		return (int) $m[1];
	}
	if ( preg_match( '/\bwp-image-(\d+)\b/', $html, $m ) ) {
		return (int) $m[1];
	}
	return 0;
}

/** @return int[] */
function merge_excluded( $existing, array $ids ): array {
	$current = is_array( $existing ) ? $existing : array_filter( array_map( 'trim', explode( ',', (string) $existing ) ), 'strlen' );
	$merged  = array_values( array_unique( array_map( 'intval', array_merge( $current, $ids ) ) ) );
	sort( $merged );
	return $merged;
}
