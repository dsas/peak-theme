<?php
require dirname( __DIR__ ) . '/vendor/autoload.php';

foreach ( glob( dirname( __DIR__ ) . '/inc/lib/*.php' ) ?: [] as $file ) {
	require_once $file;
}
