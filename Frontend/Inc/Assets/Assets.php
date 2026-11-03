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

		// Inject dynamic colors as CSS variables. Categories and tags get their own
		// colors (with a tinted background) so the two label types are easy to tell apart.
		$settings = get_option( 'askora_settings', [] );
		$primary  = $this->color( $settings, 'primary_color', '#6C63FF' );
		$category = $this->color( $settings, 'category_color', '#6C63FF' );
		$tag      = $this->color( $settings, 'tag_color', '#0E9F8E' );

		$inline = sprintf(
			':root { --askora-primary: %1$s; --askora-category: %2$s; --askora-category-bg: %3$s; --askora-tag: %4$s; --askora-tag-bg: %5$s; }',
			$primary,
			$category,
			$this->tint( $category ),
			$tag,
			$this->tint( $tag )
		);
		wp_add_inline_style( 'askora-frontend', $inline );
	}

	/**
	 * Read a validated hex color from the settings array.
	 *
	 * @param array  $settings Plugin settings.
	 * @param string $key      Settings key.
	 * @param string $default  Fallback hex color.
	 * @return string
	 */
	private function color( array $settings, string $key, string $default ): string {
		$color = isset( $settings[ $key ] ) ? sanitize_hex_color( $settings[ $key ] ) : '';
		return $color ? $color : $default;
	}

	/**
	 * Mix a hex color with white to get a soft background tint.
	 *
	 * @param string $hex    Hex color (#rgb or #rrggbb).
	 * @param float  $amount Share of white, 0-1.
	 * @return string
	 */
	private function tint( string $hex, float $amount = 0.88 ): string {
		$hex = ltrim( $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		$rgb = [ hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) ];
		foreach ( $rgb as $i => $channel ) {
			$rgb[ $i ] = (int) round( $channel + ( 255 - $channel ) * $amount );
		}
		return sprintf( '#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2] );
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
			[ 'jquery', 'askora-frontend' ], // askora-frontend provides the AskoraData global.
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
