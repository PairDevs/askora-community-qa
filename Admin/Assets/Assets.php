<?php
/**
 * Admin assets enqueue.
 *
 * @package QuestionHub\Admin\Assets
 * @since   1.0.0
 */

namespace QuestionHub\Admin\Assets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Assets
 *
 * Enqueues admin CSS and JS — mirrors PrimeKit Admin/Assets/Assets.php.
 */
class Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	public function admin_enqueue_styles() {
		wp_enqueue_style(
			'questionhub-admin-style',
			QUESTIONHUB_URL . 'assets/css/admin.css',
			[],
			QUESTIONHUB_VERSION
		);
	}

	public function admin_enqueue_scripts( $hook_suffix ) {
		// Only load on QuestionHub admin pages.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['page'] ) || false === strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'questionhub' ) ) {
			return;
		}

		wp_enqueue_script(
			'questionhub-admin-script',
			QUESTIONHUB_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			QUESTIONHUB_VERSION,
			true
		);

		wp_localize_script(
			'questionhub-admin-script',
			'QuestionHubAdmin',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'questionhub_admin_nonce' ),
			]
		);
	}
}
