<?php
/**
 * Frontend asset enqueue.
 *
 * @package ASKORA\Frontend\Inc\Assets
 * @since   1.0.0
 */

namespace ASKORA\Frontend\Inc\Assets;

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
			'askora-frontend',
			ASKORA_URL . 'assets/css/frontend.css',
			[ 'dashicons' ],
			ASKORA_VERSION
		);

		// Inject dynamic primary color as CSS variable.
		$settings = get_option( 'askora_settings', [] );
		$color    = isset( $settings['primary_color'] ) ? $settings['primary_color'] : '#6C63FF';
		$inline   = ':root { --askora-primary: ' . sanitize_hex_color( $color ) . '; }';
		wp_add_inline_style( 'askora-frontend', $inline );
	}

	public function enqueue_scripts(): void {
		wp_enqueue_script(
			'askora-frontend',
			ASKORA_URL . 'assets/js/frontend.js',
			[ 'jquery' ],
			ASKORA_VERSION,
			true
		);

		wp_enqueue_script(
			'askora-auth',
			ASKORA_URL . 'assets/js/auth.js',
			[ 'jquery' ],
			ASKORA_VERSION,
			true
		);

		wp_localize_script(
			'askora-frontend',
			'AskoraData',
			[
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'askora_nonce' ),
				'is_logged_in' => is_user_logged_in(),
				'i18n'        => [
					'loading'         => __( 'Loading…', 'askora-community-qa' ),
					'load_more'       => __( 'Load More', 'askora-community-qa' ),
					'no_more'         => __( 'No more questions.', 'askora-community-qa' ),
					'vote_success'    => __( 'Vote recorded!', 'askora-community-qa' ),
					'already_voted'   => __( 'You have already voted.', 'askora-community-qa' ),
					'login_required'  => __( 'Please log in to perform this action.', 'askora-community-qa' ),
					'submit_success'  => __( 'Submitted successfully!', 'askora-community-qa' ),
					'error_generic'   => __( 'Something went wrong. Please try again.', 'askora-community-qa' ),
				],
			]
		);
	}
}
