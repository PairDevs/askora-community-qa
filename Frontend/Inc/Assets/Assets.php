<?php
/**
 * Frontend asset enqueue.
 *
 * @package QuestionHub\Frontend\Inc\Assets
 * @since   1.0.0
 */

namespace QuestionHub\Frontend\Inc\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function enqueue_styles(): void {
		// Dashicons must be explicitly enqueued on the frontend so badges/icons
		// display for ALL visitors — not just logged-in/admin-bar users.
		wp_enqueue_style( 'dashicons' );

		wp_enqueue_style(
			'questionhub-frontend',
			QUESTIONHUB_URL . 'assets/css/frontend.css',
			[ 'dashicons' ],
			QUESTIONHUB_VERSION
		);

		// Inject dynamic primary color as CSS variable.
		$settings = get_option( 'questionhub_settings', [] );
		$color    = isset( $settings['primary_color'] ) ? $settings['primary_color'] : '#6C63FF';
		$inline   = ':root { --questionhub-primary: ' . sanitize_hex_color( $color ) . '; }';
		wp_add_inline_style( 'questionhub-frontend', $inline );
	}

	public function enqueue_scripts(): void {
		wp_enqueue_script(
			'questionhub-frontend',
			QUESTIONHUB_URL . 'assets/js/frontend.js',
			[ 'jquery' ],
			QUESTIONHUB_VERSION,
			true
		);

		wp_enqueue_script(
			'questionhub-auth',
			QUESTIONHUB_URL . 'assets/js/auth.js',
			[ 'jquery' ],
			QUESTIONHUB_VERSION,
			true
		);

		wp_localize_script(
			'questionhub-frontend',
			'QuestionHubData',
			[
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'questionhub_nonce' ),
				'is_logged_in' => is_user_logged_in(),
				'i18n'        => [
					'loading'         => __( 'Loading…', 'questionhub' ),
					'load_more'       => __( 'Load More', 'questionhub' ),
					'no_more'         => __( 'No more questions.', 'questionhub' ),
					'vote_success'    => __( 'Vote recorded!', 'questionhub' ),
					'already_voted'   => __( 'You have already voted.', 'questionhub' ),
					'login_required'  => __( 'Please log in to perform this action.', 'questionhub' ),
					'submit_success'  => __( 'Submitted successfully!', 'questionhub' ),
					'error_generic'   => __( 'Something went wrong. Please try again.', 'questionhub' ),
				],
			]
		);
	}
}
