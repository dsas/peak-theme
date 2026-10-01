<?php
use PHPUnit\Framework\TestCase;
use function Peak\Content\wrap_leading_emoji;

final class ContentTest extends TestCase {
	public function test_wraps_the_leading_emoji_of_each_item(): void {
		$this->assertSame(
			'<ul><li><span class="peak-emoji">👪</span> Being a father</li><li><span class="peak-emoji">🏠</span> Living in <a href="/x">Chesterfield</a></li></ul>',
			wrap_leading_emoji( '<ul><li>👪 Being a father</li><li>🏠 Living in <a href="/x">Chesterfield</a></li></ul>' )
		);
	}

	public function test_keeps_multi_codepoint_emoji_together(): void {
		// Skin-tone modifier and variation selectors are part of the same emoji.
		$this->assertSame( '<li><span class="peak-emoji">✍🏻</span> Writing</li>', wrap_leading_emoji( '<li>✍🏻 Writing</li>' ) );
		$this->assertSame( '<li><span class="peak-emoji">⚒️</span> Working</li>', wrap_leading_emoji( '<li>⚒️ Working</li>' ) );
	}

	public function test_keeps_li_attributes(): void {
		$this->assertSame( '<li class="x"><span class="peak-emoji">🚴</span> Riding</li>', wrap_leading_emoji( '<li class="x">🚴 Riding</li>' ) );
	}

	public function test_leaves_items_without_a_leading_emoji_alone(): void {
		$html = '<ul><li>Plain item 🚴</li><li><a href="/x">Link</a></li><li>2026 – digits</li></ul>';
		$this->assertSame( $html, wrap_leading_emoji( $html ) );
	}
}
