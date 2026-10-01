<?php
use PHPUnit\Framework\TestCase;
use function Peak\Formats\{ is_photo_format, single_hierarchy, frontpage_hierarchy, first_image_id, merge_excluded };

final class FormatsTest extends TestCase {
	public function test_image_and_gallery_are_photo_formats(): void {
		$this->assertTrue( is_photo_format( 'image' ) );
		$this->assertTrue( is_photo_format( 'gallery' ) );
	}

	public function test_other_formats_are_not_photo_formats(): void {
		foreach ( [ false, null, '', 'standard', 'aside', 'link', 'video' ] as $format ) {
			$this->assertFalse( is_photo_format( $format ), var_export( $format, true ) );
		}
	}

	public function test_single_hierarchy_prepends_photo_template_for_photo_formats(): void {
		$this->assertSame(
			[ 'single-photo.php', 'single-post-paphos.php', 'single-post.php', 'single.php' ],
			single_hierarchy( [ 'single-post-paphos.php', 'single-post.php', 'single.php' ], 'gallery' )
		);
	}

	public function test_single_hierarchy_unchanged_for_writing(): void {
		$templates = [ 'single-post.php', 'single.php' ];
		$this->assertSame( $templates, single_hierarchy( $templates, false ) );
		$this->assertSame( $templates, single_hierarchy( $templates, 'aside' ) );
	}

	public function test_frontpage_kept_for_static_page_with_default_template(): void {
		$this->assertSame( [ 'front-page.php' ], frontpage_hierarchy( [ 'front-page.php' ], 'page', '' ) );
		$this->assertSame( [ 'front-page.php' ], frontpage_hierarchy( [ 'front-page.php' ], 'page', 'default' ) );
	}

	public function test_frontpage_skipped_when_front_page_has_its_own_template(): void {
		$this->assertSame( [], frontpage_hierarchy( [ 'front-page.php' ], 'page', 'photos-grid' ) );
	}

	public function test_frontpage_skipped_when_front_page_shows_latest_posts(): void {
		$this->assertSame( [], frontpage_hierarchy( [ 'front-page.php' ], 'posts', '' ) );
	}

	public function test_first_image_id_finds_image_nested_in_gallery(): void {
		$blocks = [
			[ 'blockName' => 'core/paragraph', 'attrs' => [], 'innerBlocks' => [], 'innerHTML' => '<p>Hi</p>' ],
			[ 'blockName' => 'core/gallery', 'attrs' => [], 'innerHTML' => '', 'innerBlocks' => [
				[ 'blockName' => 'core/image', 'attrs' => [ 'id' => 42 ], 'innerBlocks' => [], 'innerHTML' => '' ],
				[ 'blockName' => 'core/image', 'attrs' => [ 'id' => 43 ], 'innerBlocks' => [], 'innerHTML' => '' ],
			] ],
		];
		$this->assertSame( 42, first_image_id( $blocks ) );
	}

	public function test_first_image_id_reads_legacy_gallery_ids(): void {
		$blocks = [ [ 'blockName' => 'core/gallery', 'attrs' => [ 'ids' => [ 7, 8 ] ], 'innerBlocks' => [], 'innerHTML' => '' ] ];
		$this->assertSame( 7, first_image_id( $blocks ) );
	}

	public function test_first_image_id_reads_classic_gallery_shortcode(): void {
		$blocks = [ [ 'blockName' => null, 'attrs' => [], 'innerBlocks' => [], 'innerHTML' => "Intro\n[gallery link=\"file\" ids=\"12,13,14\"]" ] ];
		$this->assertSame( 12, first_image_id( $blocks ) );
	}

	public function test_first_image_id_reads_classic_img_class(): void {
		$blocks = [ [ 'blockName' => null, 'attrs' => [], 'innerBlocks' => [], 'innerHTML' => '<p><img class="alignnone size-full wp-image-99" src="x.jpg"></p>' ] ];
		$this->assertSame( 99, first_image_id( $blocks ) );
	}

	public function test_first_image_id_returns_zero_when_no_images(): void {
		$this->assertSame( 0, first_image_id( [ [ 'blockName' => 'core/paragraph', 'attrs' => [], 'innerBlocks' => [], 'innerHTML' => '<p>No pics</p>' ] ] ) );
	}

	public function test_merge_excluded_accepts_strings_and_arrays(): void {
		$this->assertSame( [ 3, 9, 10 ], merge_excluded( '3, 9', [ 9, 10 ] ) );
		$this->assertSame( [ 1, 2 ], merge_excluded( [ 1 ], [ 2 ] ) );
		$this->assertSame( [ 5 ], merge_excluded( '', [ 5 ] ) );
	}
}
