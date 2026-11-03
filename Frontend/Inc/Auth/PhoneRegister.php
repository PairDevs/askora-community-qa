<?php
/**
 * Phone number registration handler.
 *
 * @package ASKORA\Frontend\Inc\Auth
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Auth;

use ASKORA\Frontend\Inc\Helpers\Sanitizer;
use ASKORA\Frontend\Inc\Helpers\Validator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PhoneRegister {

	/**
	 * Register a new user by phone number + password.
	 *
	 * @param array $data { name, phone, password, email (optional) }
	 * @return int|\WP_Error User ID on success, WP_Error on failure.
	 */
	public function register( array $data ) {
		// $data arrives already unslashed from the AJAX handler. Sanitizer::text() unslashes
		// again, which would eat legitimate backslashes, so use the core sanitizer directly.
		$name     = sanitize_text_field( (string) ( $data['name'] ?? '' ) );
		$phone    = Sanitizer::phone( $data['phone'] ?? '' );
		$password = $data['password'] ?? '';
		$email    = isset( $data['email'] ) ? Sanitizer::email( $data['email'] ) : '';

		// Validate.
		if ( ! Validator::required( $name ) ) {
			return new \WP_Error( 'invalid_name', __( 'Full name is required.', 'askora-community-qa' ) );
		}
		if ( ! Validator::phone( $phone ) ) {
			return new \WP_Error( 'invalid_phone', __( 'Please enter a valid phone number.', 'askora-community-qa' ) );
		}
		// Passwords are not sanitized (that would corrupt them). Core trims surrounding
		// whitespace when creating the user and when authenticating, so measure the
		// trimmed value or a whitespace-only password would slip through.
		if ( mb_strlen( trim( (string) $password ) ) < 6 ) {
			return new \WP_Error( 'weak_password', __( 'Password must be at least 6 characters.', 'askora-community-qa' ) );
		}

		$settings = get_option( 'askora_settings', [] );
		if ( ! empty( $settings['email_required'] ) && empty( $email ) ) {
			return new \WP_Error( 'email_required', __( 'A valid email address is required.', 'askora-community-qa' ) );
		}

		if ( UserMeta::is_phone_taken( $phone ) ) {
			return new \WP_Error( 'phone_taken', __( 'This phone number is already registered.', 'askora-community-qa' ) );
		}

		// Build placeholder email if none provided.
		if ( empty( $email ) ) {
			$hash  = substr( md5( $phone . wp_generate_uuid4() ), 0, 12 );
			$domain = wp_parse_url( get_site_url(), PHP_URL_HOST );
			$email  = 'askora_' . $hash . '@' . $domain . '.local';
		}

		// Random username. It must NOT be derived from the phone number: a hash of a
		// phone number is reversible by brute force and would leak it via author URLs.
		do {
			$username = 'askora_' . strtolower( wp_generate_password( 12, false, false ) );
		} while ( username_exists( $username ) );

		// Create user.
		$user_id = wp_create_user( $username, $password, $email );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		// Store display name and phone. wp_update_user() expects slashed data.
		wp_update_user( [
			'ID'           => $user_id,
			'display_name' => wp_slash( $name ),
			'first_name'   => wp_slash( $name ),
		] );

		UserMeta::set_phone( $user_id, $phone );

		/**
		 * Fires after a user registers via Askora phone registration.
		 *
		 * @param int $user_id Registered user ID.
		 * @since 1.0.0
		 */
		do_action( 'askora_auth_after_register', $user_id );

		return $user_id;
	}
}
