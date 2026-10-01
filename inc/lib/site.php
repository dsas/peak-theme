<?php
/**
 * Site identity helpers. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Site;

function host( string $url ): string {
	return preg_replace( '/^www\./', '', strtolower( (string) parse_url( $url, PHP_URL_HOST ) ) );
}

/** Replace the text of the first <a> element. $text must already be escaped. */
function replace_link_text( string $html, string $text ): string {
	return preg_replace_callback( '/(<a\b[^>]*>).*?(<\/a>)/s', fn( $m ) => $m[1] . $text . $m[2], $html, 1 );
}
