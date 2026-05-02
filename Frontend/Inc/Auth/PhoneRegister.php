<?php
/**
 * Phone number registration handler.
 *
 * @package QuestionHub\Frontend\Inc\Auth
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Auth;

use QuestionHub\Frontend\Inc\Helpers\Sanitizer;
use QuestionHub\Frontend\Inc\Helpers\Validator;

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
		$name     = Sanitizer::text( $data['name'] ?? '' );
		$phone    = Sanitizer::phone( $data['phone'] ?? '' );
		$password = $data['password'] ?? '';
		$email    = isset( $data['email'] ) ? Sanitizer::email( $data['email'] ) : '';

		// Validate.
		if ( ! Validator::required( $name ) ) {
			return new \WP_Error( 'invalid_name', __( 'Full name is required.', 'questionhub' ) );
		}
		if ( ! Validator::phone( $phone ) ) {
			return new \WP_Error( 'invalid_phone', __( 'Please enter a valid phone number.', 'questionhub' ) );
		}
		if ( ! Validator::min_length( $password, 6 ) ) {
			return new \WP_Error( 'weak_password', __( 'Password must be at least 6 characters.', 'questionhub' ) );
		}
		if ( UserMeta::is_phone_taken( $phone ) ) {
			return new \WP_Error( 'phone_taken', __( 'This phone number is already registered.', 'questionhub' ) );
		}

		// Build placeholder email if none provided.
		if ( empty( $email ) ) {
			$hash  = substr( md5( $phone . wp_generate_uuid4() ), 0, 12 );
			$domain = wp_parse_url( get_site_url(), PHP_URL_HOST );
			$email  = 'questionhub_' . $hash . '@' . $domain . '.local';
		}

		// Generate unique username from phone hash.
		$username = 'qh_' . substr( md5( $phone ), 0, 10 );
		while ( username_exists( $username ) ) {
			$username = 'qh_' . substr( md5( $phone . uniqid() ), 0, 10 );
		}

		// Create user.
		$user_id = wp_create_user( $username, $password, $email );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		// Store display name and phone.
		wp_update_user( [
			'ID'           => $user_id,
			'display_name' => $name,
			'first_name'   => $name,
		] );

		UserMeta::set_phone( $user_id, $phone );

		/**
		 * Fires after a user registers via QuestionHub phone registration.
		 *
		 * @param int $user_id Registered user ID.
		 * @since 1.0.0
		 */
		do_action( 'questionhub_auth_after_register', $user_id );

		return $user_id;
	}
}
