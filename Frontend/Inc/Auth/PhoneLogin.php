<?php
/**
 * Phone number login handler.
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

class PhoneLogin {

	const ATTEMPTS_TRANSIENT = 'askora_login_attempts_';
	const MAX_ATTEMPTS       = 5;
	const LOCKOUT_SECONDS    = 900; // 15 minutes.

	/**
	 * Log in a user by phone number + password.
	 *
	 * @param string $phone    Phone number.
	 * @param string $password Plain text password.
	 * @return int|\WP_Error User ID on success.
	 */
	public function login( string $phone, string $password ) {
		$phone = Sanitizer::phone( $phone );

		if ( ! Validator::phone( $phone ) ) {
			return new \WP_Error( 'invalid_phone', __( 'Please enter a valid phone number.', 'askora-community-qa' ) );
		}

		// Brute force protection.
		$transient_key = self::ATTEMPTS_TRANSIENT . md5( $phone );
		$attempts      = (int) get_transient( $transient_key );
		if ( $attempts >= self::MAX_ATTEMPTS ) {
			return new \WP_Error( 'too_many_attempts', __( 'Too many login attempts. Please try again later.', 'askora-community-qa' ) );
		}

		// Find user by phone.
		$user_id = UserMeta::find_user_by_phone( $phone );
		if ( ! $user_id ) {
			$this->record_attempt( $transient_key, $attempts );
			return new \WP_Error( 'invalid_credentials', __( 'Invalid phone number or password.', 'askora-community-qa' ) );
		}

		$user = get_user_by( 'ID', $user_id );
		if ( ! $user || ! wp_check_password( $password, $user->user_pass, $user_id ) ) {
			$this->record_attempt( $transient_key, $attempts );
			return new \WP_Error( 'invalid_credentials', __( 'Invalid phone number or password.', 'askora-community-qa' ) );
		}

		// Clear attempts on success.
		delete_transient( $transient_key );

		// Log the user in.
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, true );

		/**
		 * Fires after successful Askora phone login.
		 *
		 * @param int $user_id User ID.
		 * @since 1.0.0
		 */
		do_action( 'askora_auth_after_login', $user_id );

		return $user_id;
	}

	private function record_attempt( string $key, int $current ): void {
		set_transient( $key, $current + 1, self::LOCKOUT_SECONDS );
	}
}
