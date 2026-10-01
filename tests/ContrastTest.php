<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use function Peak\Contrast\{ ratio, hex_colours };

final class ContrastTest extends TestCase {
	private static function load( string $file ): array {
		return json_decode( file_get_contents( dirname( __DIR__ ) . '/' . $file ), true, 512, JSON_THROW_ON_ERROR );
	}

	public static function files(): array {
		return [ 'peak' => [ 'theme.json' ], 'dusk' => [ 'styles/dusk.json' ] ];
	}

	public function test_ratio_matches_known_values(): void {
		$this->assertEqualsWithDelta( 21.0, ratio( '#000000', '#ffffff' ), 0.01 );
		$this->assertEqualsWithDelta( 1.0, ratio( '#777777', '#777777' ), 0.01 );
		$this->assertEqualsWithDelta( 21.0, ratio( '#fff', '#000' ), 0.01 );
	}

	public function test_hex_colours_extracts_gradient_stops(): void {
		$this->assertSame( [ '#1d2340', '#2f2a3c' ], hex_colours( 'linear-gradient(180deg, #1d2340 0%, #2f2a3c 100%)' ) );
	}

	#[DataProvider( 'files' )]
	public function test_text_colours_meet_aa_on_every_background( string $file ): void {
		$json        = self::load( $file );
		$palette     = array_column( $json['settings']['color']['palette'], 'color', 'slug' );
		$backgrounds = array_unique( array_merge( [ $palette['base'] ], hex_colours( $json['settings']['custom']['page-background'] ) ) );

		foreach ( [ 'contrast', 'primary', 'accent', 'muted' ] as $slug ) {
			foreach ( $backgrounds as $bg ) {
				$this->assertGreaterThanOrEqual( 4.5, ratio( $palette[ $slug ], $bg ), "$slug on $bg in $file" );
			}
		}
		// Active chip: base-coloured text on primary.
		$this->assertGreaterThanOrEqual( 4.5, ratio( $palette['base'], $palette['primary'] ), "chip in $file" );
	}

	#[DataProvider( 'files' )]
	public function test_valley_text_meets_aa( string $file ): void {
		$custom = self::load( $file )['settings']['custom'];
		foreach ( [ 'text', 'accent' ] as $key ) {
			$this->assertGreaterThanOrEqual( 4.5, ratio( $custom['valley'][ $key ], $custom['hill']['3'] ), "valley $key in $file" );
		}
	}
}
