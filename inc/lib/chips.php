<?php
/**
 * Topic chips. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Chips;

/** Insert an "All" item at the start of the first <ul>. $url and $label must already be escaped. */
function prepend_all( string $html, string $url, bool $current, string $label ): string {
	$item = sprintf(
		'<li class="cat-item cat-item-all%s"><a href="%s"%s>%s</a></li>',
		$current ? ' current-cat' : '',
		$url,
		$current ? ' aria-current="page"' : '',
		$label
	);
	return preg_replace_callback( '/<ul\b[^>]*>/', fn( $m ) => $m[0] . $item, $html, 1 );
}
