<?php
/**
 * Image aspect ratios. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Aspect;

const FALLBACK = 1.5;

function ratio( ?array $meta ): float {
	$width  = (int) ( $meta['width'] ?? 0 );
	$height = (int) ( $meta['height'] ?? 0 );
	if ( $width <= 0 || $height <= 0 ) {
		return FALLBACK;
	}
	return round( $width / $height, 4 );
}

function style_value( float $ratio ): string {
	return '--ar:' . rtrim( rtrim( number_format( $ratio, 4, '.', '' ), '0' ), '.' ) . ';';
}

function orientation( float $ratio ): string {
	return $ratio < 1 ? 'portrait' : 'landscape';
}

function post_id_from_class( string $class ): int {
	return preg_match( '/(?:^|\s)post-(\d+)(?:\s|$)/', $class, $m ) ? (int) $m[1] : 0;
}
