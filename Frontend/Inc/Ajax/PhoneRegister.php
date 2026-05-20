<?php
/**
 * AJAX: Phone Register.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Auth\PhoneRegister as PhoneRegisterHandler;
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

		do_action( 'askora_auth_before_register' );

		$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$phone    = isset( $_POST['phone'] ) ? preg_replace( '/[^0-9+\-() ]/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
		$password = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';
		$confirm  = isset( $_POST['confirm_password'] ) ? sanitize_text_field( wp_unslash( $_POST['confirm_password'] ) ) : '';
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		if ( $password !== $confirm ) {
			Response::die_error( __( 'Passwords do not match.', 'askora-community-qa' ) );
		}

		$handler = new PhoneRegisterHandler();
		$result  = $handler->register( compact( 'name', 'phone', 'password', 'email' ) );

		if ( is_wp_error( $result ) ) {
			Response::die_error( $result->get_error_message() );
		}

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
