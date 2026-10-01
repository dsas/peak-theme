<?php
/**
 * Content helpers. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Content;

/**
 * Wrap the emoji at the start of each <li> in a span, so the Emoji list style can hang it
 * in the margin. Items that don't start with an emoji are left alone.
 */
function wrap_leading_emoji( string $html ): string {
	$result = preg_replace_callback(
		// An <li> opening tag, optional whitespace, then one emoji grapheme cluster (\X keeps
		// skin tones, variation selectors and ZWJ sequences together) starting with a pictograph.
		'/(<li\b[^>]*>)\s*(?=\p{Extended_Pictographic})(\X)/u',
		fn( $m ) => $m[1] . '<span class="peak-emoji">' . $m[2] . '</span>',
		$html
	);
	return $result ?? $html;
}

/**
 * Remove block delimiters (<!-- wp:paragraph --> and the like) from text that is shown
 * as plain text, such as comments written with the block-based comment form on
 * WordPress.com. The newline after each delimiter goes too, so wpautop doesn't turn it
 * into a stray <br>, while blank lines between blocks still become paragraph breaks.
 */
function strip_block_delimiters( string $text ): string {
	return preg_replace( '/<!-- \/?wp:[^>]*?-->(?:[ \t]*\r?\n)?/', '', $text ) ?? $text;
}
