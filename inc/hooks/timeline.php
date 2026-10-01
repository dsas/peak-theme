<?php
/**
 * Data for the timeline and year rail blocks.
 *
 * @package Peak
 */

namespace Peak\Timeline;

function current_context(): array {
	if ( is_category() ) {
		return [ 'category' => get_queried_object_id() ];
	}
	if ( is_date() ) {
		return [ 'year' => (int) get_query_var( 'year' ) ];
	}
	return [];
}

/** Memoised so the timeline and the rail share one query. */
function items( array $ctx ): array {
	static $cache = [];
	$key = md5( (string) wp_json_encode( $ctx ) );
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$query = new \WP_Query( query_args( $ctx ) );
	$items = [];
	foreach ( $query->posts as $post ) {
		$categories = get_the_category( $post->ID );
		$excerpt    = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
		$items[]    = [
			'id'           => $post->ID,
			'date'         => get_post_time( 'Y-m-d', false, $post ),
			'title'        => display_title( get_the_title( $post ), $excerpt ?: wp_strip_all_tags( get_the_excerpt( $post ) ) ),
			'url'          => (string) get_permalink( $post ),
			'category'     => $categories ? $categories[0]->name : '',
			'category_url' => $categories ? (string) get_category_link( $categories[0] ) : '',
			'excerpt'      => $excerpt,
		];
	}
	return $cache[ $key ] = $items;
}
