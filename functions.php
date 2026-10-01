<?php
/**
 * Peak theme bootstrap.
 *
 * @package Peak
 */

namespace Peak;

const VERSION = '0.1.0';

foreach ( glob( __DIR__ . '/inc/lib/*.php' ) ?: [] as $peak_file ) {
	require_once $peak_file;
}
foreach ( glob( __DIR__ . '/inc/hooks/*.php' ) ?: [] as $peak_file ) {
	require_once $peak_file;
}
