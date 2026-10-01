<?php
/**
 * Photo content helpers. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Photo;

/**
 * Removes the first <img> whose src is the given file (any size suffix or query string),
 * plus a link wrapping only that image, plus a paragraph left empty by the removal.
 *
 * Importers prefix files with a five-character hash that differs between copies of the
 * same photo, so that prefix is ignored on both sides.
 *
 * @param string $file_stem Attachment file name without extension.
 */
function strip_classic_image( string $html, string $file_stem ): string {
	if ( '' === $file_stem ) {
		return $html;
	}
	$stem = preg_quote( preg_replace( '/^[0-9a-f]{5}-(?=.)/i', '', $file_stem ), '~' );
	$img  = '<img\b[^>]*\bsrc=["\'][^"\']*?(?<![\w-])(?:[0-9a-f]{5}-)?' . $stem . '(?:-\d+x\d+)?\.[^"\']*["\'][^>]*>';
	$link = '<a\b[^>]*>\s*' . $img . '\s*</a>';
	$re   = '~<p\b[^>]*>\s*(?:' . $link . '|' . $img . ')\s*</p>|' . $link . '|' . $img . '~i';

	return preg_replace( $re, '', $html, 1 ) ?? $html;
}
