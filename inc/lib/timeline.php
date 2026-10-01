<?php
/**
 * Writing timeline logic. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Timeline;

use const Peak\Formats\PHOTO_FORMATS;

function query_args( array $ctx ): array {
	$limit = (int) ( $ctx['limit'] ?? 0 );
	$args  = [
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
		'posts_per_page'      => $limit > 0 ? $limit : -1,
		'tax_query'           => [
			[
				'taxonomy' => 'post_format',
				'field'    => 'slug',
				'terms'    => array_map( fn( $format ) => 'post-format-' . $format, PHOTO_FORMATS ),
				'operator' => 'NOT IN',
			],
		],
	];
	if ( ! empty( $ctx['category'] ) ) {
		$args['cat'] = (int) $ctx['category'];
	}
	if ( ! empty( $ctx['tag_id'] ) ) {
		$args['tag_id'] = (int) $ctx['tag_id'];
	}
	if ( ! empty( $ctx['taxonomy'] ) && ! empty( $ctx['term_id'] ) ) {
		$args['tax_query']['relation'] = 'AND';
		$args['tax_query'][]           = [
			'taxonomy' => (string) $ctx['taxonomy'],
			'field'    => 'term_id',
			'terms'    => [ (int) $ctx['term_id'] ],
		];
	}
	foreach ( [ 'year', 'monthnum', 'day' ] as $key ) {
		if ( ! empty( $ctx[ $key ] ) ) {
			$args[ $key ] = (int) $ctx[ $key ];
		}
	}
	if ( ! empty( $ctx['related_to'] ) ) {
		$args['post__not_in'] = [ (int) $ctx['related_to'] ];
		if ( ! empty( $ctx['related_categories'] ) ) {
			$args['category__in'] = array_map( 'intval', $ctx['related_categories'] );
		}
	}
	return $args;
}

/** @return array<int, array> year => items, newest year first, item order preserved. */
function group_by_year( array $items ): array {
	$groups = [];
	foreach ( $items as $item ) {
		$groups[ (int) substr( $item['date'], 0, 4 ) ][] = $item;
	}
	krsort( $groups );
	return $groups;
}

/** @return int[] */
function years( array $items ): array {
	return array_keys( group_by_year( $items ) );
}

/** Untitled posts (old links/asides) still need link text. */
function display_title( string $title, string $excerpt ): string {
	if ( '' !== trim( $title ) ) {
		return $title;
	}
	$words = preg_split( '/\s+/', trim( $excerpt ), -1, PREG_SPLIT_NO_EMPTY );
	if ( ! $words ) {
		return 'Untitled';
	}
	return count( $words ) > 8 ? implode( ' ', array_slice( $words, 0, 8 ) ) . '…' : implode( ' ', $words );
}
