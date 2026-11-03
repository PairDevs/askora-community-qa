<?php
/**
 * AJAX: Phone Login.
 *
 * @package ASKORA\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Ajax;

use ASKORA\Frontend\Inc\Auth\PhoneLogin as PhoneLoginHandler;
use ASKORA\Frontend\Inc\Helpers\Permission;
use ASKORA\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PhoneLogin {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_askora_phone_login', [ $this, 'handle' ] );
		add_action( 'wp_ajax_askora_phone_login',        [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'askora_nonce', 'nonce' );

		if ( ! Permission::phone_auth_enabled() ) {
			Response::die_error( __( 'Phone login is disabled.', 'askora-community-qa' ) );
		}

		do_action( 'askora_auth_before_login' );

		$phone    = isset( $_POST['phone'] ) ? preg_replace( '/[^0-9+\-() ]/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
		// Passwords must reach the authenticator untouched: sanitize_text_field() would strip
		// tags, %xx sequences and surrounding whitespace, corrupting valid passwords.
		$password = isset( $_POST['password'] ) ? wp_unslash( $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- passwords are intentionally not sanitized.

		$handler = new PhoneLoginHandler();
		$result  = $handler->login( $phone, $password );

		if ( is_wp_error( $result ) ) {
			Response::die_error( $result->get_error_message() );
		}

		Response::success( __( 'Login successful. Redirecting…', 'askora-community-qa' ), [
			'redirect' => apply_filters( 'askora_login_redirect', home_url(), $result ),
		] );
	}
}
