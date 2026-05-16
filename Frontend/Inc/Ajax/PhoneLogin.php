<?php
/**
 * AJAX: Phone Login.
 *
 * @package QuestionHub\Frontend\Inc\Ajax
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Ajax;

use QuestionHub\Frontend\Inc\Auth\PhoneLogin as PhoneLoginHandler;
use QuestionHub\Frontend\Inc\Helpers\Response;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PhoneLogin {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_questionhub_phone_login', [ $this, 'handle' ] );
		add_action( 'wp_ajax_questionhub_phone_login',        [ $this, 'handle' ] );
	}

	public function handle(): void {
		check_ajax_referer( 'questionhub_nonce', 'nonce' );

		do_action( 'questionhub_auth_before_login' );

		$phone    = isset( $_POST['phone'] ) ? preg_replace( '/[^0-9+\-() ]/', '', sanitize_text_field( wp_unslash( $_POST['phone'] ) ) ) : '';
		$password = isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '';

		$handler = new PhoneLoginHandler();
		$result  = $handler->login( $phone, $password );

		if ( is_wp_error( $result ) ) {
			Response::die_error( $result->get_error_message() );
		}

		Response::success( __( 'Login successful. Redirecting…', 'questionhub' ), [
			'redirect' => apply_filters( 'questionhub_login_redirect', home_url(), $result ),
		] );
	}
}
