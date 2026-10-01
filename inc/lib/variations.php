<?php
/**
 * Visitor-switchable colourways from theme.json and styles/*.json. Pure functions.
 *
 * @package Peak
 */

namespace Peak\Variations;

function kebab( string $s ): string {
	return strtolower( preg_replace( '/([a-z0-9])([A-Z])/', '$1-$2', $s ) );
}

/** Same naming as WordPress: settings.custom.grid.opacity → --wp--custom--grid--opacity. */
function flatten_custom( array $custom, string $prefix = '--wp--custom' ): array {
	$out = [];
	foreach ( $custom as $key => $value ) {
		$name = $prefix . '--' . kebab( (string) $key );
		if ( is_array( $value ) ) {
			$out += flatten_custom( $value, $name );
		} else {
			$out[ $name ] = (string) $value;
		}
	}
	return $out;
}

function variables( array $json ): array {
	$vars = [];
	foreach ( $json['settings']['color']['palette'] ?? [] as $colour ) {
		$vars[ '--wp--preset--color--' . $colour['slug'] ] = (string) $colour['color'];
	}
	return $vars + flatten_custom( $json['settings']['custom'] ?? [] );
}

function slugify( string $title ): string {
	return trim( strtolower( preg_replace( '/[^a-z0-9]+/i', '-', $title ) ), '-' );
}

/**
 * @param array               $base  Decoded theme.json.
 * @param array<string,array> $files Decoded styles/*.json keyed by file slug.
 */
function collect( array $base, array $files ): array {
	$base_vars = variables( $base );
	$list      = [
		[
			'slug'   => slugify( $base['title'] ?? 'default' ) ?: 'default',
			'title'  => (string) ( $base['title'] ?? 'Default' ),
			'scheme' => (string) ( $base['settings']['custom']['scheme'] ?? 'light' ),
			'vars'   => $base_vars,
		],
	];
	foreach ( $files as $slug => $json ) {
		$own = variables( $json );
		if ( ! $own ) {
			continue;
		}
		$list[] = [
			'slug'   => (string) $slug,
			'title'  => (string) ( $json['title'] ?? $slug ),
			'scheme' => (string) ( $json['settings']['custom']['scheme'] ?? 'light' ),
			'vars'   => array_merge( $base_vars, $own ),
		];
	}
	return $list;
}

function declarations( array $vars, string $scheme ): string {
	$out = 'color-scheme:' . ( 'dark' === $scheme ? 'dark' : 'light' ) . ';';
	foreach ( $vars as $name => $value ) {
		if ( ! preg_match( '/^--[a-z0-9-]+$/', $name ) || preg_match( '/[;{}<>]/', $value ) ) {
			continue;
		}
		$out .= $name . ':' . $value . ';';
	}
	return $out;
}

function to_css( array $variations ): string {
	$css = '';
	foreach ( $variations as $v ) {
		$css .= sprintf( 'html[data-style="%1$s"],html[data-style="%1$s"] body{%2$s}', $v['slug'], declarations( $v['vars'], $v['scheme'] ) );
	}
	return $css;
}

/** With no stored choice, a dark system preference gets the first dark variation (works without JS). */
function prefers_dark_css( array $variations ): string {
	foreach ( $variations as $v ) {
		if ( 'dark' === $v['scheme'] ) {
			return '@media (prefers-color-scheme: dark){html:not([data-style]),html:not([data-style]) body{' . declarations( $v['vars'], 'dark' ) . '}}';
		}
	}
	return '';
}
