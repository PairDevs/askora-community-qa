<?php
/**
 * PHPUnit bootstrap for Askora Community Q&A.
 *
 * Needs the WordPress test library. Install it with:
 *   bin/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
 * using a DEDICATED database - the suite wipes its tables.
 *
 * This directory is excluded from the WordPress.org package (see .distignore).
 *
 * @package ASKORA
 */

$askora_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $askora_tests_dir ) {
	$askora_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Yoast PHPUnit Polyfills are required by the WordPress test suite.
if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills' );
}

if ( ! file_exists( $askora_tests_dir . '/includes/functions.php' ) ) {
	fwrite( STDERR, "WordPress test library not found in {$askora_tests_dir}.\nRun bin/install-wp-tests.sh first (see tests/bootstrap.php).\n" );
	exit( 1 );
}

require_once $askora_tests_dir . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	static function () {
		require dirname( __DIR__ ) . '/askora-community-qa.php';
	}
);

require $askora_tests_dir . '/includes/bootstrap.php';

require_once __DIR__ . '/phpunit/AskoraTestCase.php';
require_once __DIR__ . '/phpunit/AskoraAjaxTestCase.php';
