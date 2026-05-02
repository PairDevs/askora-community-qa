<?php
/**
 * Validator helper.
 *
 * @package QuestionHub\Frontend\Inc\Helpers
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Validator
 *
 * Input validation helpers for Q&A forms and auth forms.
 */
class Validator {

	public static function required( $value ) {
		return '' !== trim( (string) $value );
	}

	public static function phone( $phone ) {
		$cleaned = preg_replace( '/[^0-9]/', '', $phone );
		return strlen( $cleaned ) >= 7 && strlen( $cleaned ) <= 15;
	}

	public static function min_length( $value, $min ) {
		return mb_strlen( trim( $value ) ) >= $min;
	}

	public static function max_length( $value, $max ) {
		return mb_strlen( trim( $value ) ) <= $max;
	}

	public static function passwords_match( $pass1, $pass2 ) {
		return $pass1 === $pass2;
	}

	public static function email( $email ) {
		return is_email( $email );
	}
}
