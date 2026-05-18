<?php
/**
 * Admin assets enqueue.
 *
 * @package ASKORA\Admin\Assets
 * @since   1.0.0
 */

namespace ASKORA\Admin\Assets;

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
			'askora-admin-style',
			ASKORA_URL . 'assets/css/admin.css',
			[],
			ASKORA_VERSION
		);
	}

	public function admin_enqueue_scripts( $hook_suffix ) {
		// Only load on Askora admin pages.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['page'] ) || false === strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'askora-community-qa' ) ) {
			return;
		}

		wp_enqueue_script(
			'askora-admin-script',
			ASKORA_URL . 'assets/js/admin.js',
			[ 'jquery' ],
			ASKORA_VERSION,
			true
		);

		wp_localize_script(
			'askora-admin-script',
			'AskoraAdmin',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'askora_admin_nonce' ),
			]
		);
	}
}
