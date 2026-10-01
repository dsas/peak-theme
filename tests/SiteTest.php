<?php
use PHPUnit\Framework\TestCase;
use function Peak\Site\{ host, replace_link_text };

final class SiteTest extends TestCase {
	public function test_host_strips_scheme_path_and_www(): void {
		$this->assertSame( 'deansas.org', host( 'https://www.deansas.org/' ) );
		$this->assertSame( 'photos.deansas.org', host( 'https://photos.deansas.org' ) );
		$this->assertSame( 'localhost', host( 'http://localhost:8881' ) );
	}

	public function test_replace_link_text_replaces_only_the_first_link_text(): void {
		$html = '<p class="wp-block-site-title"><a href="https://deansas.org/" rel="home">Dean Sas</a></p>';
		$this->assertSame(
			'<p class="wp-block-site-title"><a href="https://deansas.org/" rel="home">deansas.org</a></p>',
			replace_link_text( $html, 'deansas.org' )
		);
	}

	public function test_replace_link_text_leaves_html_without_links_alone(): void {
		$this->assertSame( '<p>Dean Sas</p>', replace_link_text( '<p>Dean Sas</p>', 'x' ) );
	}

	public function test_replace_link_text_is_safe_with_dollar_signs(): void {
		$this->assertSame( '<a href="/">$1 shop</a>', replace_link_text( '<a href="/">x</a>', '$1 shop' ) );
	}
}
