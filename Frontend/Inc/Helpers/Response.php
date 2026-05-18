<?php
/**
 * Response helper.
 *
 * @package ASKORA\Frontend\Inc\Helpers
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Response
 *
 * Thin wrappers around wp_send_json_success/error for AJAX handlers.
 */
class Response {

	public static function success( $message = '', $data = [] ) {
		wp_send_json_success( array_merge( [ 'message' => $message ], $data ) );
	}

	public static function error( $message = '', $data = [] ) {
		wp_send_json_error( array_merge( [ 'message' => $message ], $data ) );
	}

	public static function die_error( $message = '' ) {
		wp_send_json_error( [ 'message' => $message ] );
		wp_die();
	}
}
