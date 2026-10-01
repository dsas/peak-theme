<?php
/**
 * WCAG contrast. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Contrast;

function luminance( string $hex ): float {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$channels = array_map(
		function ( $pair ) {
			$v = hexdec( $pair ) / 255;
			return $v <= 0.03928 ? $v / 12.92 : ( ( $v + 0.055 ) / 1.055 ) ** 2.4;
		},
		str_split( $hex, 2 )
	);
	return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
}

function ratio( string $a, string $b ): float {
	$la = luminance( $a );
	$lb = luminance( $b );
	return ( max( $la, $lb ) + 0.05 ) / ( min( $la, $lb ) + 0.05 );
}

/** @return string[] Every #rrggbb / #rgb in a CSS value, in order. */
function hex_colours( string $value ): array {
	preg_match_all( '/#(?:[0-9a-f]{6}|[0-9a-f]{3})\b/i', $value, $m );
	return $m[0];
}
