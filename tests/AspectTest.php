<?php
use PHPUnit\Framework\TestCase;
use function Peak\Aspect\{ ratio, style_value, orientation, post_id_from_class, sizes_attr, portrait_hero_sizes };

final class AspectTest extends TestCase {
	public function test_ratio_from_metadata(): void {
		$this->assertSame( 1.5, ratio( [ 'width' => 3000, 'height' => 2000 ] ) );
		$this->assertSame( 4.0, ratio( [ 'width' => 6000, 'height' => 1500 ] ) );
		$this->assertSame( 0.6667, ratio( [ 'width' => 2000, 'height' => 3000 ] ) );
	}

	public function test_ratio_falls_back_for_missing_or_broken_metadata(): void {
		$this->assertSame( 1.5, ratio( null ) );
		$this->assertSame( 1.5, ratio( [] ) );
		$this->assertSame( 1.5, ratio( [ 'width' => 1200, 'height' => 0 ] ) );
	}

	public function test_style_value_trims_trailing_zeros(): void {
		$this->assertSame( '--ar:1.5;', style_value( 1.5 ) );
		$this->assertSame( '--ar:1.3333;', style_value( 4 / 3 ) );
		$this->assertSame( '--ar:2;', style_value( 2.0 ) );
	}

	public function test_orientation(): void {
		$this->assertSame( 'portrait', orientation( 0.75 ) );
		$this->assertSame( 'landscape', orientation( 1.0 ) );
		$this->assertSame( 'landscape', orientation( 4.0 ) );
	}

	public function test_post_id_from_class(): void {
		$this->assertSame( 123, post_id_from_class( 'wp-block-post post-123 post type-post status-publish format-image' ) );
		$this->assertSame( 0, post_id_from_class( 'wp-block-post type-post status-publish' ) );
	}

	public function test_sizes_attr_from_row_heights(): void {
		$this->assertSame( '(max-width: 600px) 165px, 450px', sizes_attr( 1.5 ) );
		$this->assertSame( '(max-width: 600px) 460px, 1255px', sizes_attr( 4.1811 ) );
	}

	public function test_sizes_attr_caps_at_1440(): void {
		$this->assertSame( '(max-width: 600px) 440px, 1200px', sizes_attr( 4.0 ) );
		$this->assertSame( '(max-width: 600px) 1100px, 1440px', sizes_attr( 10.0 ) );
	}

	public function test_portrait_hero_sizes_use_screen_height_on_landscape_screens(): void {
		$this->assertSame( '(orientation: portrait) 100vw, calc(92vh * 0.6667)', portrait_hero_sizes( 0.6667 ) );
		$this->assertSame( '(orientation: portrait) 100vw, calc(92vh * 0.75)', portrait_hero_sizes( 0.75 ) );
	}
}
