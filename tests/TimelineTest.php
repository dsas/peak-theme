<?php
use PHPUnit\Framework\TestCase;
use function Peak\Timeline\{ query_args, group_by_year, years, display_title };

final class TimelineTest extends TestCase {
	public function test_default_query_is_all_writing_newest_first(): void {
		$args = query_args( [] );
		$this->assertSame( -1, $args['posts_per_page'] );
		$this->assertSame( 'DESC', $args['order'] );
		$this->assertSame( 'NOT IN', $args['tax_query'][0]['operator'] );
		$this->assertSame( [ 'post-format-image', 'post-format-gallery' ], $args['tax_query'][0]['terms'] );
		$this->assertArrayNotHasKey( 'cat', $args );
		$this->assertArrayNotHasKey( 'year', $args );
	}

	public function test_category_year_and_limit(): void {
		$args = query_args( [ 'category' => 5, 'year' => 2019, 'limit' => 3 ] );
		$this->assertSame( 5, $args['cat'] );
		$this->assertSame( 2019, $args['year'] );
		$this->assertSame( 3, $args['posts_per_page'] );
	}

	public function test_related_excludes_current_post_and_matches_categories(): void {
		$args = query_args( [ 'related_to' => 77, 'related_categories' => [ 2, 4 ], 'limit' => 3 ] );
		$this->assertSame( [ 77 ], $args['post__not_in'] );
		$this->assertSame( [ 2, 4 ], $args['category__in'] );
	}

	public function test_group_by_year_keeps_order_within_year_and_sorts_years_desc(): void {
		$items = [
			[ 'date' => '2026-09-12', 'title' => 'a' ],
			[ 'date' => '2026-01-02', 'title' => 'b' ],
			[ 'date' => '2019-05-01', 'title' => 'c' ],
		];
		$groups = group_by_year( $items );
		$this->assertSame( [ 2026, 2019 ], array_keys( $groups ) );
		$this->assertSame( [ 'a', 'b' ], array_column( $groups[2026], 'title' ) );
	}

	public function test_years_skip_gaps(): void {
		$items = [ [ 'date' => '2022-01-01' ], [ 'date' => '2020-06-01' ], [ 'date' => '2020-01-01' ] ];
		$this->assertSame( [ 2022, 2020 ], years( $items ) );
		$this->assertSame( [], years( [] ) );
	}

	public function test_display_title_falls_back_to_excerpt_then_placeholder(): void {
		$this->assertSame( 'Back from sabbatical', display_title( 'Back from sabbatical', 'ignored' ) );
		$this->assertSame( 'A great link about keyboards and why I…', display_title( '', 'A great link about keyboards and why I keep buying them' ) );
		$this->assertSame( 'Untitled', display_title( '   ', '' ) );
	}

	public function test_tag_context(): void {
		$args = query_args( [ 'tag_id' => 12 ] );
		$this->assertSame( 12, $args['tag_id'] );
	}

	public function test_taxonomy_context_is_anded_with_photo_exclusion(): void {
		$args = query_args( [ 'taxonomy' => 'post_format', 'term_id' => 31 ] );
		$this->assertSame( 'AND', $args['tax_query']['relation'] );
		$this->assertSame( 'NOT IN', $args['tax_query'][0]['operator'] );
		$this->assertSame( [ 'taxonomy' => 'post_format', 'field' => 'term_id', 'terms' => [ 31 ] ], $args['tax_query'][1] );
	}

	public function test_no_taxonomy_leaves_tax_query_without_relation(): void {
		$this->assertArrayNotHasKey( 'relation', query_args( [] )['tax_query'] );
	}

	public function test_month_and_day(): void {
		$args = query_args( [ 'year' => 2012, 'monthnum' => 5, 'day' => 9 ] );
		$this->assertSame( 2012, $args['year'] );
		$this->assertSame( 5, $args['monthnum'] );
		$this->assertSame( 9, $args['day'] );
		$this->assertArrayNotHasKey( 'monthnum', query_args( [ 'year' => 2012 ] ) );
	}
}
