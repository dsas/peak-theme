<?php
use PHPUnit\Framework\TestCase;
use function Peak\Exif\{ format, shutter };

final class ExifTest extends TestCase {
	public function test_formats_typical_camera_details(): void {
		$meta = [ 'camera' => 'X-T5', 'focal_length' => '23', 'aperture' => '8', 'shutter_speed' => '0.004', 'iso' => '200' ];
		$this->assertSame( [ 'X-T5', '23mm', 'f/8', '1/250s', 'ISO 200' ], format( $meta ) );
	}

	public function test_keeps_meaningful_decimals(): void {
		$this->assertSame( [ '18.5mm', 'f/2.8' ], format( [ 'focal_length' => '18.5', 'aperture' => '2.8' ] ) );
	}

	public function test_skips_empty_and_zero_values(): void {
		$this->assertSame( [], format( [ 'camera' => '', 'focal_length' => '0', 'aperture' => '0', 'shutter_speed' => '0', 'iso' => '0' ] ) );
		$this->assertSame( [], format( [] ) );
	}

	public function test_shutter_speeds(): void {
		$this->assertSame( '1/250s', shutter( 0.004 ) );
		$this->assertSame( '0.3s', shutter( 0.3333 ) );
		$this->assertSame( '0.8s', shutter( 0.8 ) );
		$this->assertSame( '1/4s', shutter( 0.25 ) );
		$this->assertSame( '2s', shutter( 2.0 ) );
		$this->assertSame( '1.5s', shutter( 1.5 ) );
	}
}
