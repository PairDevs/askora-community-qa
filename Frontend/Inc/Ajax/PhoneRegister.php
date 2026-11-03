<?php
/**
 * AJAX: Phone Register.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Auth\PhoneRegister as PhoneRegisterHandler;
use ASKORA\Frontend\Inc\Helpers\Permission;
use ASKORA\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PhoneRegister {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_askora_phone_register', [ $this, 'handle' ] );
		add_action( 'wp_ajax_askora_phone_register',        [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		if ( ! Permission::phone_auth_enabled() ) {
			Response::die_error( __( 'Phone registration is disabled.', 'askora-community-qa' ) );
		}

		/**
		 * Filters whether public phone registration is allowed.
		 *
		 * Return false to close registration (for example to follow the core
		 * "Anyone can register" setting: `add_filter( 'askora_allow_registration', fn() => (bool) get_option( 'users_can_register' ) );`).
		 *
		 * @param bool $allowed Whether registration is allowed. Default true.
		 * @since 1.0.1
		 */
		if ( ! apply_filters( 'askora_allow_registration', true ) ) {
			Response::die_error( __( 'Registration is currently closed.', 'askora-community-qa' ) );
		}

		// Honeypot: real users never see or fill this field.
		if ( ! empty( $_POST['askora_hp'] ) ) {
			Response::die_error( __( 'Registration failed. Please try again.', 'askora-community-qa' ) );
		}

		// Throttle account creation per IP address.
		$ip_key   = 'askora_reg_ip_' . md5( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
		$reg_hits = (int) get_transient( $ip_key );
		$reg_max  = (int) apply_filters( 'askora_registrations_per_hour', 5 );
		if ( $reg_max > 0 && $reg_hits >= $reg_max ) {
			Response::die_error( __( 'Too many registrations from your network. Please try again later.', 'askora-community-qa' ) );
		}

		do_action( 'askora_auth_before_register' );

		$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$phone    = isset( $_POST['phone'] ) ? preg_replace( '/[^0-9+\-() ]/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
		// Passwords are deliberately not sanitized (see PhoneLogin AJAX handler).
		$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$confirm  = isset( $_POST['confirm_password'] ) ? wp_unslash( $_POST['confirm_password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$raw_mail = isset( $_POST['email'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['email'] ) ) ) : '';
		$email    = sanitize_email( $raw_mail );

		if ( '' !== $raw_mail && ! is_email( $email ) ) {
			Response::die_error( __( 'Please enter a valid email address.', 'askora-community-qa' ) );
		}

		if ( $password !== $confirm ) {
			Response::die_error( __( 'Passwords do not match.', 'askora-community-qa' ) );
		}

		$handler = new PhoneRegisterHandler();
		$result  = $handler->register( compact( 'name', 'phone', 'password', 'email' ) );

		if ( is_wp_error( $result ) ) {
			Response::die_error( $result->get_error_message() );
		}

		set_transient( $ip_key, $reg_hits + 1, HOUR_IN_SECONDS );

		$settings = get_option( 'askora_settings', [] );
		if ( ! empty( $settings['auto_login_after_reg'] ) ) {
			$user = get_user_by( 'ID', $result );
			if ( $user ) {
				// Use wp_signon() so all WordPress authentication hooks and
				// security plugin filters are respected.
				wp_signon(
					[
						'user_login'    => $user->user_login,
						'user_password' => $password,
						'remember'      => true,
					],
					is_ssl()
				);
			}
		}

		Response::success( __( 'Account created successfully.', 'askora-community-qa' ), [
			'redirect' => apply_filters( 'askora_register_redirect', home_url(), $result ),
		] );
	}
}
