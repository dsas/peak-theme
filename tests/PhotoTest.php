<?php
use PHPUnit\Framework\TestCase;
use function Peak\Photo\strip_classic_image;

final class PhotoTest extends TestCase {
	private const STEM = '4bb5a-50974120583_c5e13bb1c0_o';

	public function test_removes_keyring_markup_entirely(): void {
		$html = '<p class="flickr-image"><a href="http://www.flickr.com/photos/x/50974120583/" class="flickr-link"><img src="https://photos.deansas.org/wp-content/uploads/2023/06/4bb5a-50974120583_c5e13bb1c0_o.jpg?w=1024&h=576" width="600" height="338" alt="Ladybower plughole" class="keyring-img" /></a></p>';
		$this->assertSame( '', trim( strip_classic_image( $html, self::STEM ) ) );
	}

	public function test_keeps_other_text_in_the_paragraph(): void {
		$html = '<p>Before <img src="/u/4bb5a-50974120583_c5e13bb1c0_o.jpg" alt=""> after</p>';
		$this->assertSame( '<p>Before  after</p>', strip_classic_image( $html, self::STEM ) );
	}

	public function test_leaves_non_matching_images(): void {
		$html = '<p><img src="/u/other.jpg" alt=""></p>';
		$this->assertSame( $html, strip_classic_image( $html, self::STEM ) );
	}

	public function test_matches_size_suffixed_variants(): void {
		$html = '<p><a href="/x"><img src="/u/4bb5a-50974120583_c5e13bb1c0_o-1024x576.jpg"></a></p>';
		$this->assertSame( '', trim( strip_classic_image( $html, self::STEM ) ) );
	}

	public function test_removes_only_the_first_match_and_keeps_following_content(): void {
		$html = '<p><img src="/u/' . self::STEM . '.jpg"></p><p>Words.</p>';
		$this->assertSame( '<p>Words.</p>', trim( strip_classic_image( $html, self::STEM ) ) );
	}

	public function test_link_with_other_content_is_kept(): void {
		$html = '<p><a href="/x">Look <img src="/u/' . self::STEM . '.jpg"></a></p>';
		$this->assertSame( '<p><a href="/x">Look </a></p>', strip_classic_image( $html, self::STEM ) );
	}

	public function test_matches_when_only_the_import_hash_prefix_differs(): void {
		$html = '<p><img src="https://example.com/2023/06/4bb5a-50974120583_c5e13bb1c0_o.jpg?w=1024"></p>';
		$this->assertSame( '', strip_classic_image( $html, '846c3-50974120583_c5e13bb1c0_o' ) );
	}

	public function test_different_photo_with_same_prefix_is_kept(): void {
		$html = '<p><img src="/u/4bb5a-99999999999_aaaaaaaaaa_o.jpg"></p>';
		$this->assertSame( $html, strip_classic_image( $html, '846c3-50974120583_c5e13bb1c0_o' ) );
	}

	public function test_empty_stem_is_unchanged(): void {
		$html = '<p><img src="/u/a.jpg"></p>';
		$this->assertSame( $html, strip_classic_image( $html, '' ) );
	}
}
