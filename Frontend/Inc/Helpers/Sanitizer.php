<?php
/**
 * Sanitizer helper.
 *
 * @package QuestionHub\Frontend\Inc\Helpers
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Sanitizer
 *
 * Plugin-specific sanitization wrappers.
 */
class Sanitizer {

	public static function text( $value ) {
		return sanitize_text_field( wp_unslash( $value ) );
	}

	public static function textarea( $value ) {
		return sanitize_textarea_field( wp_unslash( $value ) );
	}

	public static function email( $value ) {
		return sanitize_email( wp_unslash( $value ) );
	}

	public static function int( $value ) {
		return absint( $value );
	}

	public static function phone( $value ) {
		// Keep digits, +, -, (, ), spaces only.
		return preg_replace( '/[^0-9+\-() ]/', '', wp_unslash( $value ) );
	}

	public static function html( $value ) {
		return wp_kses_post( wp_unslash( $value ) );
	}
}
