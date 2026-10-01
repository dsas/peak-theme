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

/**
 * `sizes` for a justified tile: its width is ratio × row height (110px on mobile, up to 300px on desktop), capped at 1440px.
 */
function sizes_attr( float $ratio ): string {
	$mobile  = min( 1440, (int) ceil( $ratio * 110 ) );
	$desktop = min( 1440, (int) ceil( $ratio * 300 ) );
	return "(max-width: 600px) {$mobile}px, {$desktop}px";
}

/** `sizes` for a portrait hero: full width on portrait screens, otherwise screen-height-limited (92vh × ratio). */
function portrait_hero_sizes( float $ratio ): string {
	$r = rtrim( rtrim( number_format( $ratio, 4, '.', '' ), '0' ), '.' );
	return "(orientation: portrait) 100vw, calc(92vh * {$r})";
}
