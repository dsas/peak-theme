<?php
use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase {
	public function test_theme_json_is_valid_json_with_a_title(): void {
		$json = json_decode( file_get_contents( dirname( __DIR__ ) . '/theme.json' ), true );
		$this->assertSame( 'Peak', $json['title'] );
	}
}
