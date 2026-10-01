<?php
use PHPUnit\Framework\TestCase;
use function Peak\Variations\{ flatten_custom, variables, collect, to_css, prefers_dark_css };

final class VariationsTest extends TestCase {
	private const BASE = [
		'title'    => 'Peak',
		'settings' => [
			'color'  => [ 'palette' => [ [ 'slug' => 'base', 'color' => '#f4efe4' ], [ 'slug' => 'contrast', 'color' => '#2b2a26' ] ] ],
			'custom' => [ 'scheme' => 'light', 'grid' => [ 'opacity' => '0' ], 'hill' => [ '1' => '#b8c7a4' ] ],
		],
	];

	private const DUSK = [
		'title'    => 'Dusk',
		'settings' => [
			'color'  => [ 'palette' => [ [ 'slug' => 'base', 'color' => '#1d2340' ] ] ],
			'custom' => [ 'scheme' => 'dark', 'grid' => [ 'opacity' => '0.6' ] ],
		],
	];

	public function test_flatten_custom_matches_wordpress_variable_names(): void {
		$this->assertSame(
			[ '--wp--custom--grid--opacity' => '0', '--wp--custom--hill--1' => '#b8c7a4', '--wp--custom--page-background' => '#fff' ],
			flatten_custom( [ 'grid' => [ 'opacity' => '0' ], 'hill' => [ '1' => '#b8c7a4' ], 'pageBackground' => '#fff' ] )
		);
	}

	public function test_variables_include_palette_and_custom(): void {
		$vars = variables( self::BASE );
		$this->assertSame( '#f4efe4', $vars['--wp--preset--color--base'] );
		$this->assertSame( '0', $vars['--wp--custom--grid--opacity'] );
	}

	public function test_collect_puts_base_first_and_inherits_unset_values(): void {
		$list = collect( self::BASE, [ 'dusk' => self::DUSK ] );
		$this->assertSame( [ 'peak', 'dusk' ], array_column( $list, 'slug' ) );
		$this->assertSame( [ 'light', 'dark' ], array_column( $list, 'scheme' ) );
		$this->assertSame( '#1d2340', $list[1]['vars']['--wp--preset--color--base'] );
		$this->assertSame( '#2b2a26', $list[1]['vars']['--wp--preset--color--contrast'], 'inherited from base' );
		$this->assertSame( '0.6', $list[1]['vars']['--wp--custom--grid--opacity'] );
	}

	public function test_collect_skips_variations_without_colours_or_custom_values(): void {
		$list = collect( self::BASE, [ 'type-only' => [ 'title' => 'Type only', 'settings' => [ 'typography' => [] ] ] ] );
		$this->assertSame( [ 'peak' ], array_column( $list, 'slug' ) );
	}

	public function test_to_css_scopes_each_variation_and_sets_color_scheme(): void {
		$css = to_css( collect( self::BASE, [ 'dusk' => self::DUSK ] ) );
		$this->assertStringContainsString( 'html[data-style="dusk"],html[data-style="dusk"] body{', $css );
		$this->assertStringContainsString( '--wp--preset--color--base:#1d2340;', $css );
		$this->assertStringContainsString( 'color-scheme:dark;', $css );
	}

	public function test_to_css_drops_unsafe_names_and_values(): void {
		$list = [ [ 'slug' => 'x', 'title' => 'X', 'scheme' => 'light', 'vars' => [ '--ok' => 'red', '--bad' => 'red;}body{display:none', 'not-a-var' => 'blue' ] ] ];
		$css  = to_css( $list );
		$this->assertStringContainsString( '--ok:red;', $css );
		$this->assertStringNotContainsString( 'display:none', $css );
		$this->assertStringNotContainsString( 'not-a-var', $css );
	}

	public function test_prefers_dark_css_applies_first_dark_variation_when_no_choice_stored(): void {
		$css = prefers_dark_css( collect( self::BASE, [ 'dusk' => self::DUSK ] ) );
		$this->assertStringStartsWith( '@media (prefers-color-scheme: dark){html:not([data-style]),html:not([data-style]) body{', $css );
		$this->assertStringContainsString( '--wp--preset--color--base:#1d2340;', $css );
	}

	public function test_prefers_dark_css_empty_without_dark_variation(): void {
		$this->assertSame( '', prefers_dark_css( collect( self::BASE, [] ) ) );
	}

	public function test_real_theme_files_produce_two_variations(): void {
		$root  = dirname( __DIR__ );
		$list  = collect(
			json_decode( file_get_contents( "$root/theme.json" ), true ),
			[ 'dusk' => json_decode( file_get_contents( "$root/styles/dusk.json" ), true ) ]
		);
		$this->assertSame( [ 'peak', 'dusk' ], array_column( $list, 'slug' ) );
		$this->assertSame( '0.6', $list[1]['vars']['--wp--custom--grid--opacity'] );
		$this->assertArrayHasKey( '--wp--custom--hill--3', $list[0]['vars'] );
	}
}
