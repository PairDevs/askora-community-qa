<?php
/**
 * Sanitizer helper.
 *
 * Centralised sanitization wrappers for the plugin.
 * Every method unslashes the value internally before sanitizing,
 * so callers do NOT need to call wp_unslash() separately.
 *
 * Usage in AJAX handlers:
 *   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized via Sanitizer
 *   $value = Sanitizer::text( $_POST['field'] ?? '' );
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
 * Plugin-specific sanitization wrappers. All methods call wp_unslash()
 * internally before passing the value to the appropriate WP sanitizer,
 * matching the behaviour expected by WordPress coding standards.
 */
class Sanitizer {

	/**
	 * Sanitize a plain-text string (single line).
	 *
	 * Internally calls: sanitize_text_field( wp_unslash( $value ) )
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input value.
	 * @return string
	 */
	public static function text( $value ): string {
		return sanitize_text_field( wp_unslash( (string) $value ) );
	}

	/**
	 * Sanitize a multi-line textarea string.
	 *
	 * Internally calls: sanitize_textarea_field( wp_unslash( $value ) )
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input value.
	 * @return string
	 */
	public static function textarea( $value ): string {
		return sanitize_textarea_field( wp_unslash( (string) $value ) );
	}

	/**
	 * Sanitize an email address.
	 *
	 * Internally calls: sanitize_email( wp_unslash( $value ) )
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input value.
	 * @return string
	 */
	public static function email( $value ): string {
		return sanitize_email( wp_unslash( (string) $value ) );
	}

	/**
	 * Sanitize an integer (always positive).
	 *
	 * Internally calls: absint( $value )
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input value.
	 * @return int
	 */
	public static function int( $value ): int {
		return absint( $value );
	}

	/**
	 * Sanitize a phone number string.
	 *
	 * Strips everything except digits, +, -, (, ), and spaces.
	 * Internally calls wp_unslash() before the regex.
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input value.
	 * @return string
	 */
	public static function phone( $value ): string {
		return preg_replace( '/[^0-9+\-() ]/', '', wp_unslash( (string) $value ) );
	}

	/**
	 * Sanitize HTML content, allowing safe post tags.
	 *
	 * Internally calls: wp_kses_post( wp_unslash( $value ) )
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input value.
	 * @return string
	 */
	public static function html( $value ): string {
		return wp_kses_post( wp_unslash( (string) $value ) );
	}

	/**
	 * Sanitize a URL.
	 *
	 * Internally calls: esc_url_raw( wp_unslash( $value ) )
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input value.
	 * @return string
	 */
	public static function url( $value ): string {
		return esc_url_raw( wp_unslash( (string) $value ) );
	}

	/**
	 * Sanitize a key/slug string (lowercase alphanumeric + underscores/dashes).
	 *
	 * Internally calls: sanitize_key( wp_unslash( $value ) )
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input value.
	 * @return string
	 */
	public static function key( $value ): string {
		return sanitize_key( wp_unslash( (string) $value ) );
	}
}
