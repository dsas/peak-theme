<?php
/**
 * Camera details from WordPress image_meta. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Exif;

/** @return string[] e.g. [ 'X-T5', '23mm', 'f/8', '1/250s', 'ISO 200' ] */
function format( array $meta ): array {
	$out = [];
	if ( '' !== trim( (string) ( $meta['camera'] ?? '' ) ) ) {
		$out[] = trim( (string) $meta['camera'] );
	}
	if ( (float) ( $meta['focal_length'] ?? 0 ) > 0 ) {
		$out[] = trim_number( (float) $meta['focal_length'], 1 ) . 'mm';
	}
	if ( (float) ( $meta['aperture'] ?? 0 ) > 0 ) {
		$out[] = 'f/' . trim_number( (float) $meta['aperture'], 1 );
	}
	if ( (float) ( $meta['shutter_speed'] ?? 0 ) > 0 ) {
		$out[] = shutter( (float) $meta['shutter_speed'] );
	}
	if ( (int) ( $meta['iso'] ?? 0 ) > 0 ) {
		$out[] = 'ISO ' . (int) $meta['iso'];
	}
	return $out;
}

function shutter( float $seconds ): string {
	if ( $seconds >= 1 ) {
		return trim_number( $seconds, 1 ) . 's';
	}
	return '1/' . (int) round( 1 / $seconds ) . 's';
}

function trim_number( float $n, int $decimals ): string {
	return rtrim( rtrim( number_format( $n, $decimals, '.', '' ), '0' ), '.' );
}
