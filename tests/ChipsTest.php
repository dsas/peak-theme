<?php
use PHPUnit\Framework\TestCase;
use function Peak\Chips\prepend_all;

final class ChipsTest extends TestCase {
	private const LIST = '<ul class="wp-block-categories-list is-style-chips wp-block-categories"><li class="cat-item cat-item-2"><a href="/category/life/">Life</a></li></ul>';

	public function test_prepends_all_chip_as_first_item(): void {
		$html = prepend_all( self::LIST, '/posts/', false, 'All' );
		$this->assertStringContainsString( '<ul class="wp-block-categories-list is-style-chips wp-block-categories"><li class="cat-item cat-item-all"><a href="/posts/">All</a></li><li class="cat-item cat-item-2">', $html );
	}

	public function test_marks_all_chip_current(): void {
		$html = prepend_all( self::LIST, '/posts/', true, 'All' );
		$this->assertStringContainsString( '<li class="cat-item cat-item-all current-cat"><a href="/posts/" aria-current="page">All</a></li>', $html );
	}

	public function test_dollar_signs_in_url_are_not_backreferences(): void {
		$this->assertStringContainsString( 'href="/p$1/"', prepend_all( self::LIST, '/p$1/', false, 'All' ) );
	}

	public function test_html_without_list_is_unchanged(): void {
		$this->assertSame( '<p>No categories</p>', prepend_all( '<p>No categories</p>', '/posts/', false, 'All' ) );
	}
}
