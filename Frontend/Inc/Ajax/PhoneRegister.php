<?php
/**
 * AJAX: Phone Register.
 *
 * @package QuestionHub\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Ajax;

use QuestionHub\Frontend\Inc\Auth\PhoneRegister as PhoneRegisterHandler;
use QuestionHub\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PhoneRegister {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_questionhub_phone_register', [ $this, 'handle' ] );
		add_action( 'wp_ajax_questionhub_phone_register',        [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'questionhub_nonce', 'nonce' );

		do_action( 'questionhub_auth_before_register' );

		$name     = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$phone    = isset( $_POST['phone'] ) ? preg_replace( '/[^0-9+\-() ]/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
		$password = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';
		$confirm  = isset( $_POST['confirm_password'] ) ? sanitize_text_field( wp_unslash( $_POST['confirm_password'] ) ) : '';
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		if ( $password !== $confirm ) {
			Response::die_error( __( 'Passwords do not match.', 'questionhub' ) );
		}

		$handler = new PhoneRegisterHandler();
		$result  = $handler->register( compact( 'name', 'phone', 'password', 'email' ) );

		if ( is_wp_error( $result ) ) {
			Response::die_error( $result->get_error_message() );
		}

		$settings = get_option( 'questionhub_settings', [] );
		if ( ! empty( $settings['auto_login_after_reg'] ) ) {
			wp_set_current_user( $result );
			wp_set_auth_cookie( $result, true );
		}

		Response::success( __( 'Account created successfully.', 'questionhub' ), [
			'redirect' => apply_filters( 'questionhub_register_redirect', home_url(), $result ),
		] );
	}
}
